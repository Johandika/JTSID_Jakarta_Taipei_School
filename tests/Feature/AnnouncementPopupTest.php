<?php

namespace Tests\Feature;

use App\Filament\Resources\AnnouncementPopups\Pages\CreateAnnouncementPopup;
use App\Filament\Resources\AnnouncementPopups\Pages\EditAnnouncementPopup;
use App\Models\AnnouncementPopup;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AnnouncementPopupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferenceDataSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_school_level_query_does_not_control_popup_visibility(): void
    {
        AnnouncementPopup::factory()->create(['title' => 'Whole school celebration']);

        $this->get('/?school_level=sd')->assertSee('Whole school celebration')->assertSessionMissing('school_level');
        $this->get('/?school_level=sma')->assertSee('Whole school celebration')->assertSessionMissing('school_level');
    }

    public function test_popup_schedule_excludes_future_expired_and_disabled_content_and_respects_priority(): void
    {
        $this->freezeTime();
        AnnouncementPopup::factory()->create(['title' => 'Future popup', 'starts_at' => now()->addMinute()]);
        AnnouncementPopup::factory()->create(['title' => 'Expired popup', 'ends_at' => now()]);
        AnnouncementPopup::factory()->create(['title' => 'Disabled popup', 'is_active' => false]);
        $active = AnnouncementPopup::factory()->create(['title' => 'Scheduled celebration', 'starts_at' => now(), 'ends_at' => now()->addHour(), 'sort_order' => 1]);
        AnnouncementPopup::factory()->create(['title' => 'Lower priority celebration', 'sort_order' => 10]);

        $this->get('/id')->assertSee('Scheduled celebration')->assertDontSee('Future popup')->assertDontSee('Expired popup')
            ->assertDontSee('Disabled popup')->assertDontSee('Lower priority celebration');
        $active->update(['is_active' => false]);
        $this->get('/id')->assertDontSee('Scheduled celebration')->assertSee('Lower priority celebration');
    }

    public function test_admin_can_upload_schedule_edit_and_disable_a_popup(): void
    {
        Storage::fake('public');
        $this->actingAs(User::where('email', 'admin@jts.local')->first());

        Livewire::test(CreateAnnouncementPopup::class)->fillForm([
            'title' => 'Congratulations JTS', 'body' => 'Our students did well.',
            'image' => UploadedFile::fake()->image('congratulations.png'),
            'link_url' => '/news', 'is_active' => true,
            'starts_at' => '2026-09-10 08:00:00', 'ends_at' => '2026-09-20 18:00:00', 'sort_order' => 2,
        ])->call('create')->assertHasNoFormErrors();
        $popup = AnnouncementPopup::where('title', 'Congratulations JTS')->firstOrFail();
        Storage::disk('public')->assertExists($popup->image);
        $this->assertSame('2026-09-20 18:00:00', $popup->ends_at->format('Y-m-d H:i:s'));
        $this->assertDatabaseHas('audit_logs', ['entity_type' => 'AnnouncementPopup', 'entity_id' => $popup->id, 'action' => 'created']);

        Livewire::test(EditAnnouncementPopup::class, ['record' => $popup->id])
            ->fillForm(['is_active' => false])->call('save')->assertHasNoFormErrors();
        $this->assertFalse($popup->fresh()->is_active);
    }

    public function test_invalid_schedule_and_unsafe_link_cannot_be_saved(): void
    {
        $this->actingAs(User::where('email', 'admin@jts.local')->first());

        Livewire::test(CreateAnnouncementPopup::class)->fillForm([
            'title' => 'Invalid popup', 'starts_at' => '2026-09-20 08:00:00', 'ends_at' => '2026-09-10 08:00:00',
            'link_url' => 'javascript:alert(1)',
        ])->call('create')->assertHasFormErrors(['ends_at', 'link_url']);
        $this->assertDatabaseMissing('announcement_popups', ['title' => 'Invalid popup']);
    }

    public function test_admin_can_set_an_expiry_without_a_start_time(): void
    {
        $this->actingAs(User::where('email', 'admin@jts.local')->first());

        Livewire::test(CreateAnnouncementPopup::class)->fillForm([
            'title' => 'Until the end of term', 'is_active' => true, 'ends_at' => '2026-12-20 18:00:00',
        ])->call('create')->assertHasNoFormErrors();

        $this->assertDatabaseHas('announcement_popups', ['title' => 'Until the end of term', 'starts_at' => null, 'ends_at' => '2026-12-20 18:00:00']);
    }

    public function test_popup_management_obeys_existing_staff_permissions(): void
    {
        $popup = AnnouncementPopup::factory()->create();
        foreach (['admin' => true, 'editor' => true, 'academic' => false, 'viewer' => false] as $username => $allowed) {
            $gate = Gate::forUser(User::where('email', $username.'@jts.local')->first());
            $this->assertSame($allowed, $gate->allows('create', AnnouncementPopup::class));
            $this->assertSame($allowed, $gate->allows('update', $popup));
            $this->assertSame($allowed, $gate->allows('delete', $popup));
        }
        $this->get('/admin/announcement-popups')->assertRedirect('/admin/login');
        $this->actingAs(User::where('email', 'viewer@jts.local')->first())->get('/admin/announcement-popups/create')->assertForbidden();
    }

    public function test_popup_text_is_escaped(): void
    {
        AnnouncementPopup::factory()->create(['title' => '<script>alert(1)</script>', 'body' => '<img src=x onerror=alert(1)>']);

        $this->get('/')->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    }
}
