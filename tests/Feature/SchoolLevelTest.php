<?php

namespace Tests\Feature;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\SubjectResources\Pages\CreateSubjectResource;
use App\Filament\Resources\SubjectResources\Pages\EditSubjectResource;
use App\Livewire\ContentCatalog;
use App\Models\AnnouncementPopup;
use App\Models\Department;
use App\Models\GradeLevel;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\StaffProfile;
use App\Models\SubjectResource;
use App\Models\SubjectResourceType;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    public function test_resource_fields_follow_the_selected_type_on_create_and_edit(): void
    {
        Storage::fake('local');
        $this->actingAs(User::where('email', 'admin@jts.local')->first());
        $form = Livewire::test(CreateSubjectResource::class);

        foreach (SubjectResourceType::all() as $type) {
            $form->set('data.type_id', $type->id)
                ->assertFormFieldVisible('file_path')
                ->assertDontSee('Isi Konten')
                ->assertDontSee('SEO Meta Title');

            if ($type->code === 'weekly_news') {
                $form->assertFormFieldVisible('image')->assertSee(__('admin.dashboard.summary'));
            } else {
                $form->assertFormFieldHidden('image')->assertDontSee(__('admin.dashboard.summary'));
            }

            if (in_array($type->code, ['calendar', 'syllabus', 'textbook'], true)) {
                $form->assertFormFieldVisible('class_level')->assertFormFieldVisible('semester')
                    ->assertFormFieldHidden('school_levels');
            } else {
                $form->assertFormFieldHidden('class_level')->assertFormFieldHidden('semester')
                    ->assertFormFieldVisible('school_levels');
            }
        }

        $form->fillForm([
            'topic' => 'Semester calendar',
            'type_id' => SubjectResourceType::where('code', 'calendar')->value('id'),
            'department_id' => Department::first()->id,
            'class_level' => 'tk2',
            'semester' => 'semester_1',
            'file_path' => UploadedFile::fake()->create('calendar.pdf', 10, 'application/pdf'),
            'upload_date' => today()->toDateString(),
            'status' => 'draft',
            'translations' => [['locale' => 'en', 'title' => 'Semester calendar']],
        ])->call('create')->assertHasNoFormErrors();

        $resource = SubjectResource::where('topic', 'Semester calendar')->firstOrFail();
        $this->assertSame('tk2', $resource->class_level);
        $this->assertNull($resource->image);
        Storage::disk('local')->assertExists($resource->file_path);

        Livewire::test(EditSubjectResource::class, ['record' => $resource->id])
            ->assertFormFieldHidden('image')
            ->set('data.type_id', SubjectResourceType::where('code', 'weekly_news')->value('id'))
            ->assertFormFieldVisible('image')
            ->assertSee(__('admin.dashboard.summary'))
            ->call('save')->assertHasNoFormErrors();
        $this->assertNull($resource->fresh()->semester);
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
        foreach (['General progress' => [], 'Shared progress' => ['sd', 'smp'], 'SMA progress' => ['sma']] as $title => $levels) {
            $resource = SubjectResource::create([
                'topic' => $title, 'department_id' => Department::first()->id,
                'type_id' => SubjectResourceType::where('code', 'lesson_progress')->value('id'),
                'school_levels' => $levels, 'file_path' => 'documents/progress.pdf', 'upload_date' => today(), 'status' => 'published',
            ]);
            $resource->translations()->create(['locale' => 'en', 'title' => $title]);
        }
        $legacy = SubjectResource::create([
            'topic' => 'TK legacy progress', 'department_id' => Department::first()->id,
            'type_id' => SubjectResourceType::where('code', 'lesson_progress')->value('id'),
            'grade_level_id' => GradeLevel::where('code', 'tk')->value('id'),
            'file_path' => 'documents/progress.pdf', 'upload_date' => today(), 'status' => 'published',
        ]);
        $legacy->translations()->create(['locale' => 'en', 'title' => 'TK legacy progress']);

        $this->get('/resources/lesson_progress?school_level=sd')->assertSee('General progress')->assertSee('Shared progress')
            ->assertDontSee('SMA progress')->assertDontSee('TK legacy progress');
        $this->get('/resources/lesson_progress?school_level=tk')->assertSee('General progress')->assertSee('TK legacy progress')->assertDontSee('Shared progress');
        $this->assertSame(['tk'], $legacy->fresh()->school_levels);
    }

    public function test_calendar_syllabus_and_textbook_use_class_level_and_semester_filters_only(): void
    {
        foreach ([
            ['calendar', 'TK1 semester 1 calendar', 'tk1', 'semester_1'],
            ['calendar', 'TK2 semester 1 calendar', 'tk2', 'semester_1'],
            ['calendar', 'SMP 1 semester 1 calendar', 'smp_1', 'semester_1'],
            ['calendar', 'SMP 1 semester 2 calendar', 'smp_1', 'semester_2'],
            ['calendar', 'SMA 1 semester 1 calendar', 'sma_1', 'semester_1'],
            ['syllabus', 'SMP 1 syllabus', 'smp_1', 'semester_1'],
            ['textbook', 'SMA 2 textbook', 'sma_2', 'semester_2'],
        ] as [$type, $title, $classLevel, $semester]) {
            $resource = SubjectResource::create([
                'topic' => $title,
                'department_id' => Department::first()->id,
                'type_id' => SubjectResourceType::where('code', $type)->value('id'),
                'class_level' => $classLevel,
                'semester' => $semester,
                'school_levels' => ['sma'],
                'grade_level_id' => GradeLevel::where('code', 'sma')->value('id'),
                'file_path' => 'documents/resource.pdf',
                'upload_date' => today(),
                'status' => 'published',
            ]);
            $resource->translations()->create(['locale' => 'en', 'title' => $title]);
        }

        $this->get('/resources/calendar?school_level=sma&class_level=smp_1&semester=semester_1')
            ->assertSee('SMP 1 semester 1 calendar')
            ->assertSee('SMP 1 - Semester 1')
            ->assertDontSee('SMP 1 semester 2 calendar')
            ->assertDontSee('SMA 1 semester 1 calendar')
            ->assertSee('aria-label="Class"', false)
            ->assertSee('aria-label="Semester"', false)
            ->assertDontSee('aria-label="School level"', false);

        Livewire::test(ContentCatalog::class, ['kind' => 'resources', 'type' => 'calendar'])
            ->assertSee('Class')
            ->assertSee('Semester')
            ->assertDontSee('School level')
            ->assertSee('TK1')
            ->assertSee('TK2')
            ->set('classLevel', 'tk1')
            ->set('semester', 'semester_1')
            ->assertSee('TK1 semester 1 calendar')
            ->assertDontSee('TK2 semester 1 calendar')
            ->set('classLevel', 'smp_1')
            ->set('semester', 'semester_2')
            ->assertSee('SMP 1 semester 2 calendar')
            ->assertDontSee('SMP 1 semester 1 calendar')
            ->set('classLevel', 'college')
            ->assertSet('classLevel', '')
            ->set('semester', '')
            ->assertSee('SMA 1 semester 1 calendar');

        Livewire::test(ContentCatalog::class, ['kind' => 'resources', 'type' => 'weekly_news'])
            ->assertSee('School level')
            ->assertDontSee('Class')
            ->assertDontSee('Semester');

        $calendar = SubjectResource::where('topic', 'SMP 1 semester 1 calendar')->firstOrFail();
        $this->assertSame('smp_1', $calendar->class_level);
        $this->assertSame('semester_1', $calendar->semester);
        $this->assertSame([], $calendar->school_levels);
        $this->assertNull($calendar->grade_level_id);
    }

    public function test_migration_splits_legacy_tk_class_level_into_tk1(): void
    {
        DB::table('subject_resources')->insert([
            'topic' => 'Legacy TK calendar',
            'department_id' => Department::first()->id,
            'type_id' => SubjectResourceType::where('code', 'calendar')->value('id'),
            'class_level' => 'tk',
            'semester' => 'semester_1',
            'school_levels' => json_encode([]),
            'file_path' => 'documents/resource.pdf',
            'upload_date' => today(),
            'status' => 'published',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration = require database_path('migrations/2026_10_07_105230_split_subject_resource_tk_class_level.php');
        $migration->up();

        $this->assertDatabaseHas('subject_resources', [
            'topic' => 'Legacy TK calendar',
            'class_level' => 'tk1',
        ]);

        $migration->down();

        $this->assertDatabaseHas('subject_resources', [
            'topic' => 'Legacy TK calendar',
            'class_level' => 'tk',
        ]);
    }

    public function test_global_pages_staff_highlights_and_popups_ignore_school_level_query(): void
    {
        foreach (['Elementary teacher', 'Senior high teacher', 'School office'] as $name) {
            StaffProfile::create(['name' => $name, 'title' => $name, 'department_id' => Department::first()->id, 'is_active' => true]);
        }
        AnnouncementPopup::factory()->create(['title' => 'Whole school celebration']);

        $this->get('/staff?school_level=sd')->assertSee('Elementary teacher')->assertSee('Senior high teacher')->assertSee('School office');
        $this->get('/?school_level=sma')->assertSee('Whole school celebration')->assertSessionMissing('school_level');
        foreach (['/', '/school-profile', '/staff', '/university-admission'] as $url) {
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

        $this->get('/jts-secure-portal/posts/create')->assertOk()->assertSeeText(__('admin.labels.school_levels'));
        $this->get('/jts-secure-portal/subject-resources/create')->assertOk()->assertSeeText(__('admin.labels.school_levels'));
        foreach (['staff-profiles', 'announcement-popups'] as $resource) {
            $this->get('/jts-secure-portal/'.$resource.'/create')->assertOk()->assertDontSeeText('School levels');
        }
    }

    public function test_admin_can_change_a_single_level_resource_to_multiple_levels_and_then_the_whole_school(): void
    {
        $resource = SubjectResource::create([
            'topic' => 'Existing resource', 'department_id' => Department::first()->id,
            'type_id' => SubjectResourceType::where('code', 'lesson_progress')->value('id'), 'grade_level_id' => GradeLevel::where('code', 'sd')->value('id'),
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
            'type_id' => SubjectResourceType::where('code', 'lesson_progress')->value('id'), 'grade_level_id' => GradeLevel::where('code', 'sd')->value('id'),
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
        foreach (['staff_profiles', 'announcement_popups'] as $tableName) {
            $this->assertFalse(Schema::hasColumn($tableName, 'school_levels'));
        }

        $staff = StaffProfile::create([
            'name' => 'Migration teacher', 'title' => 'Teacher',
            'department_id' => Department::first()->id, 'is_active' => true,
        ]);
        $migration = require database_path('migrations/2026_09_20_173012_remove_school_levels_from_global_content_tables.php');
        $migration->down();

        foreach (['staff_profiles', 'announcement_popups'] as $tableName) {
            $this->assertTrue(Schema::hasColumn($tableName, 'school_levels'));
        }
        DB::table('staff_profiles')->where('id', $staff->id)->update(['school_levels' => json_encode(['sd'])]);

        $migration->up();

        foreach (['staff_profiles', 'announcement_popups'] as $tableName) {
            $this->assertFalse(Schema::hasColumn($tableName, 'school_levels'));
        }
        $this->assertDatabaseHas('staff_profiles', ['id' => $staff->id, 'name' => 'Migration teacher']);
    }
}
