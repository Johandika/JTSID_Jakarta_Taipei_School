<?php

namespace Tests\Feature;

use App\Filament\Resources\AdmissionForms\Pages\EditAdmissionForm;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\SubjectResources\Pages\CreateSubjectResource;
use App\Filament\Widgets\DashboardHeaderWidget;
use App\Filament\Widgets\LatestContentWidget;
use App\Filament\Widgets\NeedsAttentionWidget;
use App\Livewire\ContentCatalog;
use App\Models\AdmissionForm;
use App\Models\AnnouncementPopup;
use App\Models\Department;
use App\Models\GradeLevel;
use App\Models\HeroSlide;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\SubjectResource;
use App\Models\SubjectResourceType;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_public_pages_and_all_locales_render(): void
    {
        foreach (['', '/id', '/zh'] as $locale) {
            foreach (['/', '/school-profile', '/academics', '/admissions', '/news', '/news/campus-story-1', '/resources', '/resources/calendar', '/staff', '/university-admission', '/contact'] as $path) {
                $response = $this->get($locale.$path);
                $this->assertSame(200, $response->status(), 'Failed public URL: '.$locale.$path);
            }
        }
        $this->get('/id')->assertSee('Kabar terbaru JTS');
        $this->get('/zh')->assertSee('校園最新消息');
        $this->get('/resources')
            ->assertOk()
            ->assertDontSeeText('All resources');
        $this->get('/resources/weekly_news')
            ->assertOk()
            ->assertSee('weekly-news-', false)
            ->assertSee('images/classroom.webp', false)
            ->assertSee('/download/resources/', false)
            ->assertDontSee('<table', false);
    }

    public function test_static_pages_are_localized_and_removed_from_the_admin_and_database(): void
    {
        $this->assertFalse(Schema::hasTable('pages'));
        $this->assertFalse(Schema::hasTable('page_translations'));

        $localizedPages = [
            '' => [
                'summary' => 'Discover our community, learning, and life at Jakarta Taipei School.',
                'titles' => ['academics' => 'Academics', 'admissions' => 'Admissions', 'board-of-directors' => 'Board of Directors', 'school-profile' => 'School Profile', 'contact' => 'Location & Contact', 'mandarin-program' => 'Short-term Mandarin Program'],
            ],
            '/id' => [
                'summary' => 'Mengenal komunitas, kegiatan belajar, dan kehidupan di Jakarta Taipei School.',
                'titles' => ['academics' => 'Akademik', 'admissions' => 'Pendaftaran', 'board-of-directors' => 'Dewan Pengurus', 'school-profile' => 'Profil Sekolah', 'contact' => 'Lokasi & Kontak', 'mandarin-program' => 'Program Mandarin Jangka Pendek'],
            ],
            '/zh' => [
                'summary' => '認識雅加達臺灣學校的社群、學習與校園生活。',
                'titles' => ['academics' => '課程設計', 'admissions' => '招生資訊', 'board-of-directors' => '理董事會', 'school-profile' => '學校概況', 'contact' => '交通資訊與聯絡方式', 'mandarin-program' => '短期華語課程'],
            ],
        ];

        foreach ($localizedPages as $prefix => $locale) {
            foreach ($locale['titles'] as $slug => $title) {
                $response = $this->get($prefix.'/'.$slug)
                    ->assertOk()
                    ->assertSeeText($title)
                    ->assertDontSee('<aside', false)
                    ->assertDontSeeText('Sample content for local testing')
                    ->assertDontSeeText('Konten contoh untuk pengujian lokal')
                    ->assertDontSeeText('此為本機測試用範例內容');

                if (! in_array($slug, ['board-of-directors', 'admissions', 'mandarin-program', 'contact'], true)) {
                    $response->assertSeeText($locale['summary'])
                        ->assertSee('class="prose"', false);
                } elseif ($slug === 'board-of-directors') {
                    $response->assertSee('images/board-of-directors-hero.webp', false);
                } elseif ($slug === 'mandarin-program') {
                    $response->assertSeeText($locale['summary']);
                } elseif ($slug === 'contact') {
                    $response->assertSee('google.com/maps/embed', false);
                } else {
                    $response->assertSeeText($locale['summary'])
                        ->assertSee('images/admissions-hero.webp', false);
                }
            }
            foreach (['about', 'history', 'parent-association'] as $removedSlug) {
                $this->get($prefix.'/'.$removedSlug)->assertNotFound();
            }
        }

        $this->get('/academics')->assertSeeText('Indonesian, Pancasila, and Religions');
        $this->get('/id/admissions')
            ->assertSee('href="/id/mandarin-program"', false)
            ->assertSeeText('Mulai perjalanan Anda di JTS')
            ->assertSee('images/admissions-hero.webp', false);
        $this->get('/zh/contact')
            ->assertSee('href="https://wa.me/6281385435909"', false)
            ->assertSeeText('透過 WhatsApp 聯絡');
        $this->get('/page-that-does-not-exist')->assertNotFound();

        $this->actingAs(User::where('email', 'admin@jts.local')->first());
        $this->get('/jts-secure-portal/pages')->assertNotFound();
        $this->get('/jts-secure-portal/pages/create')->assertNotFound();
    }

    public function test_pages_removal_migration_can_be_rolled_back(): void
    {
        $migration = require database_path('migrations/2026_09_20_154150_remove_pages_tables.php');
        $migration->down();

        $this->assertTrue(Schema::hasTable('pages'));
        $this->assertTrue(Schema::hasTable('page_translations'));

        DB::table('audit_logs')->insert([
            'action' => 'deleted',
            'entity_type' => 'Page',
            'entity_id' => 1,
        ]);

        $migration->up();

        $this->assertFalse(Schema::hasTable('pages'));
        $this->assertFalse(Schema::hasTable('page_translations'));
        $this->assertDatabaseMissing('audit_logs', ['entity_type' => 'Page']);
    }

    public function test_demo_cleanup_migration_only_changes_exact_legacy_content(): void
    {
        $notices = [
            'en' => '> Sample content for local testing. This is not an official school announcement.',
            'id' => '> Konten contoh untuk pengujian lokal. Informasi ini belum menjadi pengumuman resmi sekolah.',
            'zh-TW' => '> 此為本機測試用範例內容，並非學校正式公告。',
        ];

        foreach ($notices as $locale => $notice) {
            $posts = DB::table('post_translations')->where('locale', $locale)->orderBy('id')->limit(2)->pluck('id');
            DB::table('post_translations')->where('id', $posts[0])->update(['body' => $notice."\n\n".'Approved article body.']);
            DB::table('post_translations')->where('id', $posts[1])->update(['body' => 'Custom body. '.$notice]);

            $resources = DB::table('subject_resource_translations')->where('locale', $locale)->orderBy('id')->limit(2)->pluck('id');
            DB::table('subject_resource_translations')->where('id', $resources[0])->update(['body' => $notice]);
            DB::table('subject_resource_translations')->where('id', $resources[1])->update(['body' => 'Custom resource body. '.$notice]);
        }

        $slides = DB::table('hero_slides')->orderBy('id')->limit(2)->pluck('id');
        DB::table('hero_slides')->where('id', $slides[0])->update(['cta_url' => '/about']);
        DB::table('hero_slides')->where('id', $slides[1])->update(['cta_url' => '/admissions']);

        $staffFieldsMigration = require database_path('migrations/2026_09_24_155756_remove_extension_and_responsibilities_from_staff_profiles_table.php');
        $staffFieldsMigration->down();

        try {
            $migration = require database_path('migrations/2026_09_24_001353_clean_public_demo_content_and_about_hero_links.php');
            $migration->up();
        } finally {
            $staffFieldsMigration->up();
        }

        foreach ($notices as $locale => $notice) {
            $this->assertSame('Approved article body.', DB::table('post_translations')->where('locale', $locale)->orderBy('id')->value('body'));
            $this->assertSame('Custom body. '.$notice, DB::table('post_translations')->where('locale', $locale)->orderBy('id')->skip(1)->value('body'));
            $this->assertNull(DB::table('subject_resource_translations')->where('locale', $locale)->orderBy('id')->value('body'));
            $this->assertSame('Custom resource body. '.$notice, DB::table('subject_resource_translations')->where('locale', $locale)->orderBy('id')->skip(1)->value('body'));
        }

        $this->assertSame('/school-profile', DB::table('hero_slides')->where('id', $slides[0])->value('cta_url'));
        $this->assertSame('/admissions', DB::table('hero_slides')->where('id', $slides[1])->value('cta_url'));
    }

    public function test_seeded_public_content_has_no_demo_notices(): void
    {
        foreach (['', '/id', '/zh'] as $prefix) {
            foreach (['/news/campus-story-1', '/resources/calendar', '/staff'] as $path) {
                $this->get($prefix.$path)
                    ->assertOk()
                    ->assertDontSeeText('Sample content for local testing')
                    ->assertDontSeeText('Konten contoh untuk pengujian lokal')
                    ->assertDontSeeText('此為本機測試用範例內容')
                    ->assertDontSeeText('Sample profile for local testing');
            }
        }

        $this->assertFalse(HeroSlide::where('cta_url', '/about')->exists());
    }

    public function test_navigation_is_static_localized_and_removed_from_the_admin_and_database(): void
    {
        $this->assertFalse(Schema::hasTable('menu_items'));
        $this->assertFalse(Schema::hasTable('menu_item_translations'));

        $localizedNavigation = [
            '/' => [
                'labels' => ['About JTS', 'Academics', 'Admissions', 'News & Media', 'Subject Resources', 'University Admission', 'Board of Directors', 'School Profile', 'Our Team', 'Contact', 'Short-term Mandarin Program'],
                'removed_labels' => ['Our History', 'Parent Association'],
                'main_navigation' => 'Main navigation',
                'mobile_navigation' => 'Mobile navigation',
                'child_url' => '/board-of-directors',
            ],
            '/id' => [
                'labels' => ['Tentang JTS', 'Akademik', 'Pendaftaran', 'Berita & Media', 'Materi Pelajaran', 'Penerimaan Universitas', 'Dewan Pengurus', 'Profil Sekolah', 'Tim Kami', 'Kontak', 'Program Mandarin Jangka Pendek'],
                'removed_labels' => ['Sejarah Sekolah', 'Asosiasi Orang Tua'],
                'main_navigation' => 'Navigasi utama',
                'mobile_navigation' => 'Navigasi seluler',
                'child_url' => '/id/board-of-directors',
            ],
            '/zh' => [
                'labels' => ['認識學校', '課程設計', '招生資訊', '新聞與媒體', '學習資源', '大學錄取榜', '理董事會', '學校概況', '校園團隊', '聯絡我們', '短期華語課程'],
                'removed_labels' => ['學校歷史', '家長委員會'],
                'main_navigation' => '主要導覽',
                'mobile_navigation' => '行動版導覽',
                'child_url' => '/zh/board-of-directors',
            ],
        ];

        foreach ($localizedNavigation as $url => $expected) {
            $response = $this->get($url)->assertOk();

            foreach ($expected['labels'] as $label) {
                $response->assertSee($label);
            }

            foreach ($expected['removed_labels'] as $label) {
                $response->assertDontSeeText($label);
            }

            $response
                ->assertSee('aria-label="'.$expected['main_navigation'].'"', false)
                ->assertSee('aria-label="'.$expected['mobile_navigation'].'"', false)
                ->assertSee('aria-controls="about-desktop-submenu"', false)
                ->assertSee('aria-controls="about-mobile-submenu"', false)
                ->assertDontSee('href="'.rtrim($url, '/').'/about"', false)
                ->assertDontSee('href="'.rtrim($url, '/').'/history"', false)
                ->assertDontSee('href="'.rtrim($url, '/').'/parent-association"', false)
                ->assertSee('href="'.$expected['child_url'].'"', false);
        }

        $this->actingAs(User::where('email', 'admin@jts.local')->first())
            ->get('/jts-secure-portal/menu-items')
            ->assertNotFound();
    }

    public function test_navigation_removal_migration_can_be_rolled_back(): void
    {
        $migration = require database_path('migrations/2026_09_20_151535_remove_menu_items_tables.php');
        $migration->down();

        $this->assertTrue(Schema::hasTable('menu_items'));
        $this->assertTrue(Schema::hasTable('menu_item_translations'));

        DB::table('audit_logs')->insert([
            'action' => 'deleted',
            'entity_type' => 'MenuItem',
            'entity_id' => 1,
        ]);

        $migration->up();

        $this->assertFalse(Schema::hasTable('menu_items'));
        $this->assertFalse(Schema::hasTable('menu_item_translations'));
        $this->assertDatabaseMissing('audit_logs', ['entity_type' => 'MenuItem']);
    }

    public function test_contacts_are_static_and_removed_from_the_admin_and_database(): void
    {
        $this->assertFalse(Schema::hasTable('contact_channels'));

        foreach (['', '/id', '/zh'] as $prefix) {
            $this->get($prefix.'/')
                ->assertOk()
                ->assertSee('href="tel:+62214523273"', false)
                ->assertSee('href="tel:+62214530535"', false)
                ->assertSee('href="mailto:jts@jtsid.org"', false)
                ->assertSee('href="https://wa.me/6281385435909"', false)
                ->assertSee('href="https://www.facebook.com/profile.php?id=100057615161035"', false)
                ->assertSee('href="https://www.instagram.com/jakartataipeischoolofficial/"', false)
                ->assertSee('href="https://youtube.com/@jakartataipeischool?si=5LP55sxC05ZOteLH"', false)
                ->assertSee('src="'.asset('images/qr-whatsapp.webp').'"', false);

            $this->get($prefix.'/contact')
                ->assertOk()
                ->assertSeeText('Jl. Raya Kelapa Hybrida Blok QA3, Kelapa Gading, Jakarta Utara 14250, Indonesia')
                ->assertSeeText('+62 (21) 452 3273')
                ->assertSeeText('+62 (21) 453 0535')
                ->assertSeeText('jts@jtsid.org');
        }

        $this->actingAs(User::where('email', 'admin@jts.local')->firstOrFail());
        $this->get('/jts-secure-portal/contact-channels')->assertNotFound();
        $this->get('/jts-secure-portal/contact-channels/create')->assertNotFound();
        $this->get('/jts-secure-portal/contact-channels/1/edit')->assertNotFound();
    }

    public function test_contacts_removal_migration_can_be_rolled_back(): void
    {
        $migration = require database_path('migrations/2026_09_22_002145_remove_contact_channels_table.php');
        $migration->down();

        $this->assertTrue(Schema::hasTable('contact_channels'));

        DB::table('audit_logs')->insert([
            'action' => 'deleted',
            'entity_type' => 'ContactChannel',
            'entity_id' => 1,
        ]);

        $migration->up();

        $this->assertFalse(Schema::hasTable('contact_channels'));
        $this->assertDatabaseMissing('audit_logs', ['entity_type' => 'ContactChannel']);
    }

    public function test_drafts_future_posts_and_untranslated_content_are_not_public(): void
    {
        $this->get('/news/campus-story-14')->assertNotFound();
        $post = Post::where('slug', 'campus-story-1')->first();
        $post->update(['published_at' => now()->addDay()]);
        $this->get('/news/'.$post->slug)->assertNotFound();
        $this->get('/download/news/'.$post->id)->assertNotFound();
        $post->update(['published_at' => now()->subDay()]);
        $post->translations()->where('locale', 'en')->delete();
        $this->get('/news/'.$post->slug)->assertNotFound();
    }

    public function test_translation_falls_back_as_a_complete_record_and_html_is_sanitized(): void
    {
        $post = Post::where('slug', 'campus-story-1')->first();
        $post->translations()->where('locale', 'id')->delete();
        $post->translations()->where('locale', 'en')->update(['body' => '<script>alert(1)</script> [bad](javascript:alert%281%29)']);
        app()->setLocale('id');
        $this->assertSame('en', $post->translation()->locale);
        $this->assertStringNotContainsString('<script>', $post->html_body);
        $this->assertStringNotContainsString('href="javascript:', $post->html_body);
    }

    public function test_search_filter_pagination_and_locale_survive_livewire_updates(): void
    {
        $catalog = Livewire::test(ContentCatalog::class, ['kind' => 'news'])
            ->assertSee('13 results')->assertSee('A new chapter of learning begins')
            ->set('search', 'no-such-story')->assertSee('No results found')
            ->call('clearFilters')->assertSee('13 results');
        $catalog->call('setPage', 2)->assertSet('paginators.page', 2);
        $catalog->set('department', (string) Department::where('code', 'academic')->value('id'))->assertSet('paginators.page', 1)->assertSee('7 results');
        app()->setLocale('id');
        Livewire::test(ContentCatalog::class, ['kind' => 'resources', 'type' => 'calendar'])
            ->assertSee('4 hasil')->set('schoolLevel', GradeLevel::first()->code)->assertSee('1 hasil')->assertSee('Tanggal unggah');
    }

    public function test_download_is_attachment_and_draft_resource_is_hidden(): void
    {
        $resource = SubjectResource::first();
        $this->get('/download/resources/'.$resource->id)->assertOk()->assertHeader('x-content-type-options', 'nosniff')->assertDownload();
        $resource->update(['status' => 'draft']);
        $this->get('/download/resources/'.$resource->id)->assertNotFound();
        $this->get('/download/unknown/1')->assertNotFound();
    }

    public function test_admissions_page_links_to_admin_uploaded_registration_form(): void
    {
        Storage::fake('public');
        $this->get('/admissions')
            ->assertOk()
            ->assertSeeText('Download registration form')
            ->assertSee('downloadForms', false);

        $this->get('/download/admission-form')->assertNotFound();

        Storage::disk('public')->put('admission-forms/admission-form-tk.pdf', '%PDF-1.4 form');

        AdmissionForm::where('school_level', 'tk')->firstOrFail()->update([
            'title' => 'Form Pendaftaran JTS',
            'file_path' => 'admission-forms/admission-form-tk.pdf',
            'is_active' => true,
        ]);

        $this->get('/admissions')
            ->assertOk()
            ->assertSee('admission-form-tk.pdf', false)
            ->assertSee('form-pendaftaran-jts.pdf', false);

        $this->get('/download/admission-form')
            ->assertOk()
            ->assertHeader('x-content-type-options', 'nosniff')
            ->assertDownload('form-pendaftaran-jts.pdf');
    }

    public function test_admin_routes_require_login_and_viewer_cannot_mutate(): void
    {
        $this->get('/admin')->assertNotFound();
        $this->get('/jts-secure-portal/posts')->assertRedirect('/jts-secure-portal/login');
        $viewer = User::where('email', 'viewer@jts.local')->first();
        $this->actingAs($viewer)->get('/jts-secure-portal/posts')->assertOk();
        $this->get('/jts-secure-portal/posts/create')->assertForbidden();
        $this->get('/jts-secure-portal/users')->assertForbidden();
        $this->assertFalse(Gate::forUser($viewer)->allows('update', Post::first()));
    }

    public function test_admin_navigation_uses_sidebar_branding_and_user_menu_without_a_topbar(): void
    {
        $response = $this->actingAs(User::where('email', 'admin@jts.local')->firstOrFail())
            ->get('/jts-secure-portal');

        $response
            ->assertOk()
            ->assertDontSee('class="fi-topbar"', false)
            ->assertSee('class="fi-sidebar-header-logo-ctn"', false)
            ->assertSee('class="fi-sidebar-footer ', false)
            ->assertSee('class="fi-user-menu', false)
            ->assertSee('jts-user-menu-language', false)
            ->assertDontSee('mb-2 rounded-lg border border-gray-200 bg-white/80', false)
            ->assertSee('x-float.placement.right-end', false)
            ->assertDontSee('Filament\\Livewire\\GlobalSearch', false);

        $content = $response->getContent();
        $positions = collect(['Content', 'Website', 'School', 'Admissions', 'Settings'])
            ->mapWithKeys(fn (string $label): array => [$label => strpos($content, $label)]);

        $positions->each(fn (int|false $position, string $label) => $this->assertNotFalse($position, "Missing sidebar group [{$label}]"));
        $this->assertLessThan($positions['Website'], $positions['Content']);
        $this->assertLessThan($positions['School'], $positions['Website']);
        $this->assertLessThan($positions['Admissions'], $positions['School']);
        $this->assertLessThan($positions['Settings'], $positions['Admissions']);
    }

    public function test_admin_dashboard_can_switch_to_traditional_chinese(): void
    {
        $this->actingAs(User::where('email', 'admin@jts.local')->firstOrFail())
            ->get('/jts-secure-portal/language/zh-TW')
            ->assertRedirect();

        $this->get('/jts-secure-portal')
            ->assertOk()
            ->assertSeeText('儀表板')
            ->assertSeeText('內容管理')
            ->assertSeeText('新聞與媒體')
            ->assertSeeText('繁體中文')
            ->assertSeeText('查看網站');
    }

    public function test_dashboard_actions_links_and_counts_follow_user_permissions(): void
    {
        app()->setLocale('id');

        Post::query()->update(['status' => 'published']);
        SubjectResource::query()->update(['status' => 'published']);

        $departmentAdmin = User::where('email', 'academic@jts.local')->firstOrFail();
        $ownPost = Post::where('department_id', $departmentAdmin->department_id)->firstOrFail();
        $otherPost = Post::where('department_id', '!=', $departmentAdmin->department_id)->firstOrFail();
        $ownPost->update(['status' => 'draft']);
        $otherPost->update(['status' => 'draft']);

        $ownResource = SubjectResource::where('department_id', $departmentAdmin->department_id)->firstOrFail();
        $otherResource = SubjectResource::whereKeyNot($ownResource->id)->firstOrFail();
        $otherResource->update([
            'department_id' => Department::whereKeyNot($departmentAdmin->department_id)->value('id'),
        ]);
        $ownResource->update(['status' => 'draft']);
        $otherResource->update(['status' => 'draft']);

        $viewer = User::where('email', 'viewer@jts.local')->firstOrFail();
        $this->actingAs($viewer);
        Livewire::test(DashboardHeaderWidget::class)
            ->assertDontSeeText('Buat Berita')
            ->assertDontSeeText('Upload Materi');
        Livewire::test(LatestContentWidget::class)
            ->assertDontSee(PostResource::getUrl('edit', ['record' => $ownPost]), false);

        $this->flushSession();
        $this->actingAs($departmentAdmin);
        Livewire::test(DashboardHeaderWidget::class)
            ->assertSeeText('Buat Berita')
            ->assertSeeText('Upload Materi');
        Livewire::test(NeedsAttentionWidget::class)
            ->assertSeeText('1 draft berita menunggu rilis')
            ->assertSeeText('1 materi berstatus draft')
            ->assertDontSeeText('popup pengumuman sedang tampil');

        AnnouncementPopup::query()->delete();
        AnnouncementPopup::factory()->create(['starts_at' => now()->addDay()]);
        AnnouncementPopup::factory()->create(['starts_at' => now()->subDay(), 'ends_at' => now()->addDay()]);

        $this->flushSession();
        $this->actingAs(User::where('email', 'admin@jts.local')->firstOrFail());
        Livewire::test(NeedsAttentionWidget::class)
            ->assertSeeText('1 popup pengumuman sedang tampil');
    }

    public function test_department_admin_cannot_read_edit_or_transfer_other_departments_content(): void
    {
        $user = User::where('email', 'academic@jts.local')->first();
        $own = Post::where('department_id', $user->department_id)->first();
        $other = Post::where('department_id', '!=', $user->department_id)->first();
        $this->actingAs($user);
        $this->assertTrue(Gate::allows('update', $own));
        $this->assertFalse(Gate::allows('update', $other));
        Livewire::test(ListPosts::class)->assertCanSeeTableRecords([$own])->assertCanNotSeeTableRecords([$other]);
        $this->get('/jts-secure-portal/posts/'.$other->id.'/edit')->assertNotFound();
        $this->get('/jts-secure-portal/pages')->assertNotFound();
        $this->get('/jts-secure-portal/users')->assertForbidden();
        $this->expectException(ValidationException::class);
        $own->update(['department_id' => $other->department_id]);
    }

    public function test_admin_can_create_translate_update_and_publish_a_post(): void
    {
        $this->actingAs(User::where('email', 'admin@jts.local')->first());
        Livewire::test(CreatePost::class)->fillForm([
            'topic' => 'Integration test announcement', 'slug' => 'integration-test', 'department_id' => Department::first()->id,
            'category_id' => PostCategory::first()->id, 'upload_date' => today()->toDateString(), 'status' => 'published',
            'translations' => [['locale' => 'en', 'title' => 'Integration test announcement', 'body' => 'Approved content.'], ['locale' => 'id', 'title' => 'Pengumuman pengujian', 'body' => 'Konten yang disetujui.']],
        ])->call('create')->assertHasNoFormErrors();
        $post = Post::where('slug', 'integration-test')->firstOrFail();
        $this->assertSame(2, $post->translations()->count());
        $this->get('/id/news/integration-test')->assertOk()->assertSee('Pengumuman pengujian');
        Livewire::test(EditPost::class, ['record' => $post->id])->fillForm(['topic' => 'Updated announcement'])->call('save')->assertHasNoFormErrors();
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'topic' => 'Updated announcement']);
        $this->assertDatabaseHas('audit_logs', ['entity_type' => 'Post', 'entity_id' => $post->id, 'action' => 'updated']);
    }

    public function test_english_translation_is_required(): void
    {
        $this->actingAs(User::where('email', 'admin@jts.local')->first());
        Livewire::test(CreatePost::class)->fillForm([
            'topic' => 'No English', 'slug' => 'no-english', 'department_id' => Department::first()->id,
            'category_id' => PostCategory::first()->id, 'upload_date' => today()->toDateString(), 'status' => 'published',
            'translations' => [['locale' => 'id', 'title' => 'Tanpa English']],
        ])->call('create')->assertHasFormErrors(['translations']);
        $this->assertDatabaseMissing('posts', ['slug' => 'no-english']);
    }

    public function test_resource_upload_accepts_pdf_and_rejects_executable_and_oversized_files(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $this->actingAs(User::where('email', 'admin@jts.local')->first());
        $data = ['topic' => 'Test file', 'department_id' => Department::first()->id, 'type_id' => SubjectResourceType::where('code', 'weekly_news')->value('id'), 'grade_level_id' => GradeLevel::first()->id,
            'upload_date' => today()->toDateString(), 'status' => 'published', 'translations' => [['locale' => 'en', 'title' => 'Test resource']]];
        Livewire::test(CreateSubjectResource::class)->fillForm($data + [
            'image' => UploadedFile::fake()->image('weekly-news.jpg'),
            'file_path' => UploadedFile::fake()->create('sample.pdf', 100, 'application/pdf'),
        ])
            ->call('create')->assertHasNoFormErrors();
        $resource = SubjectResource::where('topic', 'Test file')->firstOrFail();
        Storage::disk('local')->assertExists($resource->file_path);
        Storage::disk('public')->assertExists($resource->image);
        Livewire::test(CreateSubjectResource::class)->fillForm($data + ['file_path' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php')])
            ->call('create')->assertHasFormErrors();
        Livewire::test(CreateSubjectResource::class)->fillForm($data + ['file_path' => UploadedFile::fake()->create('large.pdf', 16000, 'application/pdf')])
            ->call('create')->assertHasFormErrors();
    }

    public function test_admin_can_update_the_seeded_registration_form(): void
    {
        Storage::fake('public');
        $this->actingAs(User::where('email', 'admin@jts.local')->first());
        $form = AdmissionForm::firstOrFail();

        Livewire::test(EditAdmissionForm::class, ['record' => $form->id])
            ->fillForm([
                'title' => 'Form Pendaftaran JTS',
                'school_level' => 'tk',
                'file_path' => UploadedFile::fake()->create('form-pendaftaran.pdf', 100, 'application/pdf'),
                'is_active' => true,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Form Pendaftaran JTS', $form->fresh()->title);
        Storage::disk('public')->assertExists($form->fresh()->file_path);
    }

    public function test_all_admin_lists_and_create_forms_render(): void
    {
        $this->actingAs(User::where('email', 'admin@jts.local')->first());
        foreach (['posts', 'subject-resources', 'staff-profiles', 'university-acceptances', 'university-logos', 'hero-slides', 'departments', 'academic-years', 'users'] as $path) {
            $this->get('/jts-secure-portal/'.$path)->assertOk();
            $this->get('/jts-secure-portal/'.$path.'/create')->assertOk();
        }
        $this->get('/jts-secure-portal/audit-logs')->assertOk();
    }

    public function test_hero_slides_render_on_homepage_with_translations(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Excellence in Trilingual Education')
            ->assertSee('classroom.webp');

        $this->get('/id')
            ->assertOk()
            ->assertSee('Keunggulan Pendidikan Tiga Bahasa')
            ->assertSee('classroom.webp');

        $this->get('/zh')
            ->assertOk()
            ->assertSee('深耕三語，開創卓越教育')
            ->assertSee('classroom.webp');
    }

    public function test_empty_hero_slides_hides_hero_section_without_error(): void
    {
        HeroSlide::query()->delete();

        $this->get('/')
            ->assertOk()
            ->assertDontSee('hero-diagonal-panel');
    }
}
