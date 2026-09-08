<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\SubjectResources\Pages\CreateSubjectResource;
use App\Livewire\ContentCatalog;
use App\Models\Department;
use App\Models\GradeLevel;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\SlideshowItem;
use App\Models\SubjectResource;
use App\Models\SubjectResourceType;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
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
            foreach (['/', '/about', '/academics', '/admissions', '/news', '/news/campus-story-1', '/resources', '/resources/calendar', '/staff', '/university-admission', '/contact'] as $path) {
                $response = $this->get($locale.$path);
                $this->assertSame(200, $response->status(), 'Failed public URL: '.$locale.$path);
            }
        }
        $this->get('/id')->assertSee('Kabar terbaru JTS');
        $this->get('/zh')->assertSee('校園最新消息');
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
        $page = Page::where('slug', 'about')->first();
        $page->translations()->where('locale', 'id')->delete();
        $page->translations()->where('locale', 'en')->update(['body' => '<script>alert(1)</script> [bad](javascript:alert%281%29)']);
        app()->setLocale('id');
        $this->assertSame('en', $page->translation()->locale);
        $this->assertStringNotContainsString('<script>', $page->html_body);
        $this->assertStringNotContainsString('href="javascript:', $page->html_body);
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
            ->assertSee('4 hasil')->set('grade', (string) GradeLevel::first()->id)->assertSee('1 hasil')->assertSee('Tanggal unggah');
    }

    public function test_download_is_attachment_and_draft_resource_is_hidden(): void
    {
        $resource = SubjectResource::first();
        $this->get('/download/resources/'.$resource->id)->assertOk()->assertHeader('x-content-type-options', 'nosniff')->assertDownload();
        $resource->update(['status' => 'draft']);
        $this->get('/download/resources/'.$resource->id)->assertNotFound();
        $this->get('/download/unknown/1')->assertNotFound();
    }

    public function test_admin_routes_require_login_and_viewer_cannot_mutate(): void
    {
        $this->get('/admin/posts')->assertRedirect('/admin/login');
        $viewer = User::where('email', 'viewer@jts.local')->first();
        $this->actingAs($viewer)->get('/admin/posts')->assertOk();
        $this->get('/admin/posts/create')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
        $this->assertFalse(Gate::forUser($viewer)->allows('update', Post::first()));
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
        $this->get('/admin/posts/'.$other->id.'/edit')->assertNotFound();
        $this->get('/admin/pages')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
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
        Livewire::test(CreatePage::class)->fillForm(['slug' => 'no-english', 'template' => 'default', 'status' => 'published',
            'translations' => [['locale' => 'id', 'title' => 'Tanpa English']]])->call('create')->assertHasFormErrors(['translations']);
        $this->assertDatabaseMissing('pages', ['slug' => 'no-english']);
    }

    public function test_resource_upload_accepts_pdf_and_rejects_executable_and_oversized_files(): void
    {
        Storage::fake('local');
        $this->actingAs(User::where('email', 'admin@jts.local')->first());
        $data = ['topic' => 'Test file', 'department_id' => Department::first()->id, 'type_id' => SubjectResourceType::first()->id, 'grade_level_id' => GradeLevel::first()->id,
            'upload_date' => today()->toDateString(), 'status' => 'published', 'translations' => [['locale' => 'en', 'title' => 'Test resource']]];
        Livewire::test(CreateSubjectResource::class)->fillForm($data + ['file_path' => UploadedFile::fake()->create('sample.pdf', 100, 'application/pdf')])
            ->call('create')->assertHasNoFormErrors();
        $resource = SubjectResource::where('topic', 'Test file')->firstOrFail();
        Storage::disk('local')->assertExists($resource->file_path);
        Livewire::test(CreateSubjectResource::class)->fillForm($data + ['file_path' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php')])
            ->call('create')->assertHasFormErrors();
        Livewire::test(CreateSubjectResource::class)->fillForm($data + ['file_path' => UploadedFile::fake()->create('large.pdf', 16000, 'application/pdf')])
            ->call('create')->assertHasFormErrors();
    }

    public function test_slideshow_limit_and_replacement(): void
    {
        for ($i = 3; $i <= 10; $i++) {
            SlideshowItem::create(['topic' => 'Slide '.$i, 'image' => 'images/seed-campus.jpg', 'is_active' => true]);
        }
        $slide = SlideshowItem::first();
        $slide->update(['image' => 'images/seed-library.jpg']);
        $this->assertSame(10, SlideshowItem::where('is_active', true)->count());
        $this->expectException(ValidationException::class);
        SlideshowItem::create(['topic' => 'Slide 11', 'image' => 'images/seed-campus.jpg', 'is_active' => true]);
    }

    public function test_all_admin_lists_and_create_forms_render(): void
    {
        $this->actingAs(User::where('email', 'admin@jts.local')->first());
        foreach (['pages', 'posts', 'subject-resources', 'slideshow-items', 'contact-channels', 'staff-profiles', 'university-acceptances', 'menu-items', 'departments', 'academic-years', 'users'] as $path) {
            $this->get('/admin/'.$path)->assertOk();
            $this->get('/admin/'.$path.'/create')->assertOk();
        }
        $this->get('/admin/audit-logs')->assertOk();
    }
}
