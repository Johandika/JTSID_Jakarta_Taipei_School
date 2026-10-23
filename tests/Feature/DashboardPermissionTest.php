<?php

namespace Tests\Feature;

use App\Filament\Resources\AnnouncementPopups\AnnouncementPopupResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\SubjectResources\SubjectResourceResource;
use App\Filament\Widgets\ContentOverview;
use App\Filament\Widgets\DashboardHeaderWidget;
use App\Filament\Widgets\LatestContentWidget;
use App\Filament\Widgets\NeedsAttentionWidget;
use App\Models\Department;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Role;
use App\Models\SubjectResource;
use App\Models\SubjectResourceType;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DashboardPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ReferenceDataSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        app()->setLocale('en');
    }

    #[DataProvider('dashboardRoles')]
    public function test_dashboard_only_displays_modules_allowed_for_the_role(
        string $role,
        bool $canViewNews,
        bool $canViewResources,
        bool $canViewPopups,
        bool $canViewHeroSlides,
        bool $canViewStaff,
    ): void {
        $this->actingAs($this->createUser($role));

        $overview = Livewire::test(ContentOverview::class);
        foreach ([
            'published_news' => $canViewNews,
            'drafts' => $canViewNews,
            'resources' => $canViewResources,
            'staff' => $canViewStaff,
            'hero_slides_desc' => $canViewHeroSlides,
        ] as $label => $isVisible) {
            if ($isVisible) {
                $overview->assertSeeText(__("admin.widgets.{$label}"));
            } else {
                $overview->assertDontSeeText(__("admin.widgets.{$label}"));
            }
        }

        $attention = Livewire::test(NeedsAttentionWidget::class);
        foreach ([
            PostResource::getUrl('index') => $canViewNews,
            SubjectResourceResource::getUrl('index') => $canViewResources,
            AnnouncementPopupResource::getUrl('index') => $canViewPopups,
        ] as $url => $isVisible) {
            if ($isVisible) {
                $attention->assertSee($url, false);
            } else {
                $attention->assertDontSee($url, false);
            }
        }

        $header = Livewire::test(DashboardHeaderWidget::class);
        foreach ([
            PostResource::getUrl('create') => $canViewNews,
            SubjectResourceResource::getUrl('create') => $canViewResources,
        ] as $url => $isVisible) {
            if ($isVisible) {
                $header->assertSee($url, false);
            } else {
                $header->assertDontSee($url, false);
            }
        }

        $dashboard = $this->get(route('filament.admin.pages.dashboard'))->assertOk();
        if ($canViewNews) {
            $dashboard->assertSeeLivewire(LatestContentWidget::class);
        } else {
            $dashboard->assertDontSeeLivewire(LatestContentWidget::class);
        }
    }

    /**
     * @return array<string, array{string, bool, bool, bool, bool, bool}>
     */
    public static function dashboardRoles(): array
    {
        return [
            'super admin' => ['super_admin', true, true, true, true, true],
            'admissions' => ['admissions', true, false, false, false, false],
            'academic' => ['academic', true, true, true, false, false],
            'student affairs' => ['student_affairs', true, true, true, false, false],
            'CLC' => ['clc', true, false, true, false, false],
            'kindergarten' => ['kindergarten', true, true, true, true, false],
            'secretary' => ['secretary', true, true, true, false, false],
            'languages' => ['languages', true, true, true, false, false],
            'library' => ['library', false, true, false, false, false],
        ];
    }

    public function test_dashboard_counts_and_latest_news_stay_within_the_department_and_resource_types(): void
    {
        $this->freezeTime();
        $academicUser = $this->createUser('academic');
        $otherDepartmentId = Department::where('code', 'library')->value('id');
        $categoryId = PostCategory::where('code', 'news')->value('id');
        $ownPosts = [];
        $otherPosts = [];

        foreach (['published', 'draft'] as $status) {
            foreach ([$academicUser->department_id, $otherDepartmentId] as $departmentId) {
                $post = Post::create([
                    'slug' => "dashboard-{$departmentId}-{$status}",
                    'topic' => "Dashboard {$departmentId} {$status}",
                    'department_id' => $departmentId,
                    'category_id' => $categoryId,
                    'upload_date' => today(),
                    'status' => $status,
                    'published_at' => now()->subDay(),
                ]);
                $post->translations()->create(['locale' => 'en', 'title' => $post->topic]);
                if ($departmentId === $academicUser->department_id) {
                    $ownPosts[] = $post;
                } else {
                    $otherPosts[] = $post;
                }
            }

            foreach ([
                [$academicUser->department_id, 'lesson_progress'],
                [$academicUser->department_id, 'weekly_news'],
                [$otherDepartmentId, 'lesson_progress'],
            ] as [$departmentId, $typeCode]) {
                $resource = SubjectResource::create([
                    'topic' => "Dashboard {$typeCode} {$status}",
                    'department_id' => $departmentId,
                    'type_id' => SubjectResourceType::where('code', $typeCode)->value('id'),
                    'file_path' => 'resources/dashboard.pdf',
                    'upload_date' => today(),
                    'status' => $status,
                    'published_at' => now()->subDay(),
                ]);
                $resource->translations()->create(['locale' => 'en', 'title' => $resource->topic]);
            }
        }

        $this->actingAs($academicUser);

        Livewire::test(LatestContentWidget::class)
            ->assertCanSeeTableRecords($ownPosts)
            ->assertCanNotSeeTableRecords($otherPosts);

        $overview = Livewire::test(ContentOverview::class);
        $stats = $overview->instance()->getSchema('content')->getComponents()[0]->getChildComponents();
        $values = collect($stats)->mapWithKeys(fn (Stat $stat): array => [(string) $stat->getLabel() => $stat->getValue()])->all();
        $this->assertSame([
            __('admin.widgets.published_news') => '1',
            __('admin.widgets.resources') => '1',
            __('admin.widgets.drafts') => '1',
        ], $values);

        $attention = Livewire::test(NeedsAttentionWidget::class);
        $items = $attention->instance()->getViewData()['items'];
        $this->assertSame([1, 1, 0], array_column($items, 'count'));
    }

    public function test_library_cannot_mount_the_latest_news_widget_even_with_posts_in_its_department(): void
    {
        $libraryUser = $this->createUser('library');
        Post::create([
            'slug' => 'library-news',
            'topic' => 'Restricted library news',
            'department_id' => $libraryUser->department_id,
            'category_id' => PostCategory::where('code', 'news')->value('id'),
            'upload_date' => today(),
            'status' => 'draft',
        ]);

        $this->actingAs($libraryUser);

        Livewire::test(LatestContentWidget::class)->assertForbidden();
    }

    public function test_latest_news_widget_rechecks_access_after_the_users_role_changes(): void
    {
        $user = $this->createUser('academic');
        $this->actingAs($user);
        $widget = Livewire::test(LatestContentWidget::class)->assertOk();

        $user->update(['role_id' => Role::where('code', 'library')->value('id')]);
        $this->actingAs($user->fresh());

        $widget->call('$refresh')->assertForbidden();
    }

    private function createUser(string $role): User
    {
        return User::factory()->create([
            'role_id' => Role::where('code', $role)->value('id'),
            'is_active' => true,
        ]);
    }
}
