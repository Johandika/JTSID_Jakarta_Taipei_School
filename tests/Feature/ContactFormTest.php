<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Livewire\ContactForm;
use App\Models\ContactMessage;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferenceDataSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_contact_page_renders_successfully_with_contact_information_and_map(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSeeText('Contact Information')
            ->assertSeeText('Address')
            ->assertSeeText('Telephone')
            ->assertSeeText('Email')
            ->assertSeeText('Opening Hours')
            ->assertSee('google.com/maps/embed', false);

        $this->get('/id/contact')
            ->assertOk()
            ->assertSeeText('Lokasi & Kontak')
            ->assertSeeText('Contact Information')
            ->assertSeeText('Jam Operasional')
            ->assertSeeText('+62 (21) 452 3273')
            ->assertSeeText('jts@jtsid.org')
            ->assertSee('google.com/maps/embed', false);

        $this->get('/zh/contact')
            ->assertOk()
            ->assertSeeText('交通資訊與聯絡方式')
            ->assertSeeText('Contact Information')
            ->assertSeeText('辦公時間')
            ->assertSee('google.com/maps/embed', false);
    }

    public function test_contact_form_submits_successfully_and_stores_in_database(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi@example.com')
            ->set('phone', '08123456789')
            ->set('subject', 'admissions')
            ->set('message', 'Halo, saya ingin menanyakan mengenai pendaftaran siswa baru untuk tahun ajaran mendatang.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSuccess', true)
            ->assertSet('name', '')
            ->assertSet('email', '')
            ->assertSet('message', '');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'subject' => 'admissions',
            'status' => ContactMessage::STATUS_UNREAD,
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', '')
            ->set('email', '')
            ->set('phone', '')
            ->set('subject', '')
            ->set('message', '')
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'phone', 'subject', 'message'])
            ->assertSet('isSuccess', false);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_validates_email_format_and_message_length(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'Ani')
            ->set('email', 'not-an-email')
            ->set('subject', 'general')
            ->set('message', 'Short') // min is 10
            ->call('submit')
            ->assertHasErrors(['email', 'message']);

        // Message exceeding 500 characters
        Livewire::test(ContactForm::class)
            ->set('name', 'Ani')
            ->set('email', 'ani@example.com')
            ->set('subject', 'general')
            ->set('message', str_repeat('a', 501))
            ->call('submit')
            ->assertHasErrors(['message']);
    }

    public function test_admin_can_access_contact_messages_in_filament(): void
    {
        $admin = User::where('email', 'admin@jts.local')->first();
        $this->actingAs($admin);

        $message = ContactMessage::factory()->create([
            'name' => 'Siti Rahma',
            'email' => 'siti@example.com',
            'subject' => 'mandarin_program',
            'status' => ContactMessage::STATUS_UNREAD,
        ]);

        $this->get(ContactMessageResource::getUrl('index'))
            ->assertOk()
            ->assertSeeText('Siti Rahma');

        $this->get(ContactMessageResource::getUrl('edit', ['record' => $message]))
            ->assertOk()
            ->assertSee('siti@example.com');

        // Opening the edit view should automatically mark the message as read
        $this->assertEquals(ContactMessage::STATUS_READ, $message->fresh()->status);
    }
}
