<?php

namespace Tests\Feature;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\SubjectResources\Pages\EditSubjectResource;
use App\Livewire\ContentCatalog;
use App\Models\AnnouncementPopup;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    public function test_school_level_filters_only_the_news_catalog_and_is_not_saved_in_session(): void
    {
        $this->createPost('SD announcement', ['sd']);
        $this->createPost('SMP and SMA announcement', ['smp', 'sma']);
        $this->createPost('School holiday notice', null);
        $this->createPost('Draft announcement', ['sd'], 'draft');

        $this->get('/?school_level=sd')->assertSee('SD announcement')->assertSee('SMP and SMA announcement')
            ->assertSee('School holiday notice')->assertDontSee('Draft announcement')->assertSessionMissing('school_level');
        $this->get('/id/news?school_level=sd')->assertSee('SD announcement')->assertSee('School holiday notice')
            ->assertDontSee('SMP and SMA announcement')->assertSessionMissing('school_level');
        $this->get('/zh/news')->assertSee('SD announcement')->assertSee('SMP and SMA announcement');
        Livewire::test(ContentCatalog::class, ['kind' => 'news'])->set('search', 'announcement')
            ->set('schoolLevel', 'sd')->assertSee('SD announcement')->assertDontSee('SMP and SMA announcement')
            ->call('clearFilters')->assertSet('schoolLevel', 'all')->assertSee('SMP and SMA announcement');
        $this->get('/zh/news?school_level=sma')->assertSee('SMP and SMA announcement')->assertDontSee('SD announcement');
        Livewire::test(ContentCatalog::class, ['kind' => 'news'])->set('schoolLevel', 'college')
            ->assertSet('schoolLevel', 'all')->assertSee('SD announcement')->assertSee('SMP and SMA announcement');
        $this->get('/news?school_level[]=sma')->assertOk()->assertSee('SD announcement')->assertSee('SMP and SMA announcement');
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

    public function test_global_pages_staff_highlights_and_popups_ignore_school_level_query(): void
    {
        foreach (['Elementary teacher', 'Senior high teacher', 'School office'] as $name) {
            StaffProfile::create(['name' => $name, 'title' => $name, 'department_id' => Department::first()->id, 'is_active' => true]);
            SlideshowItem::create(['topic' => $name.' highlight', 'image' => 'images/seed-campus.jpg', 'is_active' => true]);
        }
        AnnouncementPopup::factory()->create(['title' => 'Whole school celebration']);

        $this->get('/staff?school_level=sd')->assertSee('Elementary teacher')->assertSee('Senior high teacher')->assertSee('School office');
        $this->get('/?school_level=sma')->assertSee('Elementary teacher highlight')->assertSee('Senior high teacher highlight')
            ->assertSee('School office highlight')->assertSee('Whole school celebration')->assertSessionMissing('school_level');
        foreach (['/', '/about', '/staff', '/university-admission'] as $url) {
            $this->get($url)->assertDontSee('aria-label="School level"', false);
        }
        foreach (['/news' => 'School level', '/id/resources' => 'Jenjang sekolah', '/zh/news' => '學部'] as $url => $label) {
            $this->get($url)
                ->assertSee('aria-label="'.$label.'"', false)
                ->assertSee('<option value="all">', false)
                ->assertSee('<option value="tk">TK</option>', false)
                ->assertSee('<option value="sd">SD</option>', false)
                ->assertSee('<option value="smp">SMP</option>', false)
                ->assertSee('<option value="sma">SMA</option>', false);
        }
    }

    public function test_a_direct_published_post_link_is_accessible_despite_the_filter(): void
    {
        $post = $this->createPost('Published SMA story', ['sma']);

        $this->get('/news/'.$post->slug.'?school_level=sd')->assertSee('Published SMA story');
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

    public function test_school_level_admin_field_only_exists_for_news_and_resources(): void
    {
        $this->actingAs(User::where('email', 'admin@jts.local')->first());

        $this->get('/admin/posts/create')->assertOk()->assertSeeText('Jenjang Sekolah');
        $this->get('/admin/subject-resources/create')->assertOk()->assertSeeText('Jenjang Sekolah');
        foreach (['staff-profiles', 'slideshow-items', 'announcement-popups'] as $resource) {
            $this->get('/admin/'.$resource.'/create')->assertOk()->assertDontSeeText('School levels');
        }
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
        $globalContentMigration = require database_path('migrations/2026_09_20_173012_remove_school_levels_from_global_content_tables.php');
        $migration = require database_path('migrations/2026_09_10_234710_add_school_level_targeting_and_announcement_popups.php');
        $globalContentMigration->down();
        $migration->down();

        $migration->up();
        $globalContentMigration->up();

        $this->assertSame(['sd'], $resource->fresh()->school_levels);
        $this->assertSame('documents/resource.pdf', $resource->fresh()->file_path);
    }

    public function test_global_content_school_level_columns_are_removed_and_migration_can_be_rolled_back(): void
    {
        foreach (['staff_profiles', 'slideshow_items', 'announcement_popups'] as $tableName) {
            $this->assertFalse(Schema::hasColumn($tableName, 'school_levels'));
        }

        $staff = StaffProfile::create([
            'name' => 'Migration teacher', 'title' => 'Teacher',
            'department_id' => Department::first()->id, 'is_active' => true,
        ]);
        $migration = require database_path('migrations/2026_09_20_173012_remove_school_levels_from_global_content_tables.php');
        $migration->down();

        foreach (['staff_profiles', 'slideshow_items', 'announcement_popups'] as $tableName) {
            $this->assertTrue(Schema::hasColumn($tableName, 'school_levels'));
        }
        DB::table('staff_profiles')->where('id', $staff->id)->update(['school_levels' => json_encode(['sd'])]);

        $migration->up();

        foreach (['staff_profiles', 'slideshow_items', 'announcement_popups'] as $tableName) {
            $this->assertFalse(Schema::hasColumn($tableName, 'school_levels'));
        }
        $this->assertDatabaseHas('staff_profiles', ['id' => $staff->id, 'name' => 'Migration teacher']);
    }
}
