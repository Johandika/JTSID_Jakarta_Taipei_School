<?php

namespace Tests\Feature;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\SubjectResources\Pages\EditSubjectResource;
use App\Livewire\ContentCatalog;
use App\Models\Department;
use App\Models\GradeLevel;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\SlideshowItem;
use App\Models\StaffProfile;
use App\Models\SubjectResource;
use App\Models\SubjectResourceType;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SchoolLevelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferenceDataSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function createPost(string $title, ?array $levels, string $status = 'published'): Post
    {
        $post = Post::create([
            'topic' => $title, 'slug' => strtolower(str_replace(' ', '-', $title)),
            'department_id' => Department::first()->id, 'category_id' => PostCategory::where('code', 'announcement')->value('id'),
            'upload_date' => today(), 'status' => $status, 'school_levels' => $levels,
        ]);
        $post->translations()->create(['locale' => 'en', 'title' => $title]);

        return $post;
    }

    public function test_selection_filters_home_and_catalog_and_persists_across_locales(): void
    {
        $this->createPost('SD announcement', ['sd']);
        $this->createPost('SMP and SMA announcement', ['smp', 'sma']);
        $this->createPost('School holiday notice', null);
        $this->createPost('Draft announcement', ['sd'], 'draft');

        $this->get('/?school_level=sd')->assertSee('SD announcement')->assertSee('School holiday notice')
            ->assertDontSee('SMP and SMA announcement')->assertDontSee('Draft announcement')->assertSessionHas('school_level', 'sd');
        $this->get('/id/news')->assertSee('SD announcement')->assertDontSee('SMP and SMA announcement');
        Livewire::test(ContentCatalog::class, ['kind' => 'news'])->set('search', 'announcement')
            ->assertSee('SD announcement')->assertDontSee('SMP and SMA announcement')->call('clearFilters')->assertSee('School holiday notice');
        $this->get('/zh/news?school_level=sma')->assertSee('SMP and SMA announcement')->assertDontSee('SD announcement');
        $this->get('/news?school_level=all')->assertSee('SD announcement')->assertSee('SMP and SMA announcement')->assertSessionHas('school_level', 'all');
    }

    public function test_resources_support_multiple_levels_and_general_content_and_legacy_records(): void
    {
        foreach (['General calendar' => [], 'Shared calendar' => ['sd', 'smp'], 'SMA calendar' => ['sma']] as $title => $levels) {
            $resource = SubjectResource::create([
                'topic' => $title, 'department_id' => Department::first()->id,
                'type_id' => SubjectResourceType::where('code', 'calendar')->value('id'),
                'school_levels' => $levels, 'file_path' => 'documents/calendar.pdf', 'upload_date' => today(), 'status' => 'published',
            ]);
            $resource->translations()->create(['locale' => 'en', 'title' => $title]);
        }
        $legacy = SubjectResource::create([
            'topic' => 'TK legacy calendar', 'department_id' => Department::first()->id,
            'type_id' => SubjectResourceType::where('code', 'calendar')->value('id'),
            'grade_level_id' => GradeLevel::where('code', 'tk')->value('id'),
            'file_path' => 'documents/calendar.pdf', 'upload_date' => today(), 'status' => 'published',
        ]);
        $legacy->translations()->create(['locale' => 'en', 'title' => 'TK legacy calendar']);

        $this->get('/resources/calendar?school_level=sd')->assertSee('General calendar')->assertSee('Shared calendar')
            ->assertDontSee('SMA calendar')->assertDontSee('TK legacy calendar');
        $this->get('/resources/calendar?school_level=tk')->assertSee('General calendar')->assertSee('TK legacy calendar')->assertDontSee('Shared calendar');
        $this->assertSame(['tk'], $legacy->fresh()->school_levels);
    }

    public function test_staff_and_highlights_follow_selection_but_general_information_remains_available(): void
    {
        foreach (['SD teacher' => ['sd'], 'SMA teacher' => ['sma'], 'School office' => []] as $name => $levels) {
            StaffProfile::create(['name' => $name, 'title' => $name, 'department_id' => Department::first()->id, 'is_active' => true, 'school_levels' => $levels]);
            SlideshowItem::create(['topic' => $name.' highlight', 'image' => 'images/seed-campus.jpg', 'is_active' => true, 'school_levels' => $levels]);
        }

        $this->get('/staff?school_level=sd')->assertSee('SD teacher')->assertSee('School office')->assertDontSee('SMA teacher');
        $this->get('/')->assertSee('SD teacher highlight')->assertSee('School office highlight')->assertDontSee('SMA teacher highlight');
    }

    public function test_invalid_selection_does_not_replace_the_saved_preference(): void
    {
        $this->withSession(['school_level' => 'sd'])->getJson('/news?school_level[]=sma')
            ->assertUnprocessable()->assertJsonValidationErrors('school_level')->assertSessionHas('school_level', 'sd');
    }

    public function test_a_direct_published_post_link_is_accessible_despite_the_filter(): void
    {
        $post = $this->createPost('Published SMA story', ['sma']);

        $this->withSession(['school_level' => 'sd'])->get('/news/'.$post->slug)->assertSee('Published SMA story');
    }

    public function test_admin_can_publish_a_post_for_multiple_levels_and_reject_unknown_levels(): void
    {
        $this->actingAs(User::where('email', 'admin@jts.local')->first());
        $data = ['topic' => 'Shared notice', 'slug' => 'shared-notice', 'school_levels' => ['sd', 'smp'],
            'department_id' => Department::first()->id, 'category_id' => PostCategory::first()->id,
            'upload_date' => today()->toDateString(), 'status' => 'published',
            'translations' => [['locale' => 'en', 'title' => 'Shared notice']]];

        Livewire::test(CreatePost::class)->fillForm($data)->call('create')->assertHasNoFormErrors();
        $this->assertSame(['sd', 'smp'], Post::where('slug', 'shared-notice')->firstOrFail()->school_levels);
        Livewire::test(CreatePost::class)->fillForm([...$data, 'slug' => 'invalid-notice', 'school_levels' => ['college']])
            ->call('create')->assertHasFormErrors(['school_levels.0']);
        $this->assertDatabaseMissing('posts', ['slug' => 'invalid-notice']);
    }

    public function test_admin_can_change_a_single_level_resource_to_multiple_levels_and_then_the_whole_school(): void
    {
        $resource = SubjectResource::create([
            'topic' => 'Existing resource', 'department_id' => Department::first()->id,
            'type_id' => SubjectResourceType::first()->id, 'grade_level_id' => GradeLevel::where('code', 'sd')->value('id'),
            'file_path' => 'documents/resource.pdf', 'upload_date' => today(), 'status' => 'draft',
        ]);
        $resource->translations()->create(['locale' => 'en', 'title' => 'Existing resource']);
        Storage::fake('local');
        Storage::disk('local')->put('documents/resource.pdf', '%PDF-1.4 test');
        $this->actingAs(User::where('email', 'admin@jts.local')->first());

        Livewire::test(EditSubjectResource::class, ['record' => $resource->id])
            ->fillForm(['school_levels' => ['sd', 'smp']])->call('save')->assertHasNoFormErrors();
        $this->assertSame(['sd', 'smp'], $resource->fresh()->school_levels);
        $this->assertNull($resource->fresh()->grade_level_id);
        Livewire::test(EditSubjectResource::class, ['record' => $resource->id])
            ->fillForm(['school_levels' => []])->call('save')->assertHasNoFormErrors();
        $this->assertSame([], $resource->fresh()->school_levels);

    }

    public function test_migration_preserves_the_level_of_existing_resources(): void
    {
        $resource = SubjectResource::create([
            'topic' => 'Pre-existing SD resource', 'department_id' => Department::first()->id,
            'type_id' => SubjectResourceType::first()->id, 'grade_level_id' => GradeLevel::where('code', 'sd')->value('id'),
            'file_path' => 'documents/resource.pdf', 'upload_date' => today(), 'status' => 'draft',
        ]);
        $migration = require database_path('migrations/2026_09_10_234710_add_school_level_targeting_and_announcement_popups.php');
        $migration->down();

        $migration->up();

        $this->assertSame(['sd'], $resource->fresh()->school_levels);
        $this->assertSame('documents/resource.pdf', $resource->fresh()->file_path);
    }
}
