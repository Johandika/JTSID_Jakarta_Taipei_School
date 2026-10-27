<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Livewire\ContactForm;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\User;
use chillerlan\QRCode\QRCode;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Mailer\Exception\TransportException;
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
            ->assertSeeText('jtis.org@gmail.com')
            ->assertSee('google.com/maps/embed', false);

        $this->get('/zh/contact')
            ->assertOk()
            ->assertSeeText('交通資訊與聯絡方式')
            ->assertSeeText('Contact Information')
            ->assertSeeText('辦公時間')
            ->assertSee('google.com/maps/embed', false);
    }

    #[DataProvider('whatsappContactLocales')]
    public function test_contact_page_and_footer_separate_admissions_and_mandarin_whatsapp_in_each_language(
        string $prefix,
        string $admissionsLabel,
        string $mandarinLabel,
        string $selectionLabel,
    ): void {
        $this->get($prefix.'/contact')->assertOk()
            ->assertSeeText($admissionsLabel)
            ->assertSeeText($mandarinLabel)
            ->assertSeeText($selectionLabel)
            ->assertSeeText('+62 813-8543-5909')
            ->assertSeeText('+62 811-1180-5213')
            ->assertSee('href="https://wa.me/6281385435909"', false)
            ->assertSee('href="https://wa.me/6281111805213"', false)
            ->assertSee('id="jts-whatsapp-contact"', false)
            ->assertSee('data-whatsapp-channel="admissions"', false)
            ->assertSee('data-whatsapp-channel="mandarin"', false)
            ->assertSee('src="'.asset('images/qr-whatsapp.webp').'"', false)
            ->assertSee('src="'.asset('images/qr-whatsapp-mandarin.webp').'"', false)
            ->assertDontSee('site.footer_whatsapp_', false);
    }

    #[DataProvider('whatsappContactLocales')]
    public function test_header_offers_both_whatsapp_contacts_in_each_language(
        string $prefix,
        string $admissionsLabel,
        string $mandarinLabel,
        string $selectionLabel,
    ): void {
        $response = $this->get($prefix.'/contact')->assertOk();

        $header = strstr($response->getContent(), '</header>', true);
        $this->assertIsString($header);
        $this->assertStringContainsString('aria-controls="header-whatsapp-options"', $header);
        $this->assertStringContainsString('aria-label="'.$selectionLabel.'"', $header);
        $this->assertStringContainsString($admissionsLabel, $header);
        $this->assertStringContainsString('+62 813-8543-5909', $header);
        $this->assertStringContainsString('href="https://wa.me/6281385435909"', $header);
        $this->assertStringContainsString($mandarinLabel, $header);
        $this->assertStringContainsString('+62 811-1180-5213', $header);
        $this->assertStringContainsString('href="https://wa.me/6281111805213"', $header);
    }

    /**
     * @return array<string, array{string, string, string, string}>
     */
    public static function whatsappContactLocales(): array
    {
        return [
            'English' => ['', 'School Admissions WhatsApp', 'Mandarin Center WhatsApp', 'Choose a WhatsApp contact'],
            'Indonesian' => ['/id', 'WhatsApp Pendaftaran Sekolah', 'WhatsApp Mandarin Center', 'Pilih kontak WhatsApp'],
            'Traditional Chinese' => ['/zh', '招生 WhatsApp', '華語中心 WhatsApp', '選擇 WhatsApp 聯絡單位'],
        ];
    }

    public function test_whatsapp_qr_codes_open_the_correct_contact_numbers(): void
    {
        foreach ([
            'admissions' => 'https://wa.me/6281385435909',
            'mandarin' => 'https://wa.me/6281111805213',
        ] as $channel => $expectedUrl) {
            $qrPath = public_path(config('site.contact.whatsapp.'.$channel.'.qr_image'));

            $this->assertSame($expectedUrl, (string) (new QRCode)->readFromFile($qrPath));
        }
    }

    public function test_footer_replaces_facebook_with_the_configured_tiktok_link(): void
    {
        config()->set('site.contact.social.tiktok', 'https://www.tiktok.com/@jts-test-account');

        $this->get('/contact')->assertOk()
            ->assertSee('href="https://www.tiktok.com/@jts-test-account"', false)
            ->assertSee('aria-label="TikTok"', false)
            ->assertDontSee('aria-label="Facebook"', false)
            ->assertDontSee('facebook.com', false);
    }

    public function test_footer_does_not_link_to_an_unconfigured_tiktok_account(): void
    {
        config()->set('site.contact.social.tiktok', null);

        $this->get('/contact')->assertOk()
            ->assertSee('aria-label="TikTok"', false)
            ->assertSee('aria-disabled="true"', false)
            ->assertDontSee('href="https://www.tiktok.com', false)
            ->assertDontSee('facebook.com', false);
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

    public function test_contact_form_rejects_unknown_subject(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'Ani')
            ->set('email', 'ani@example.com')
            ->set('phone', '08123456789')
            ->set('subject', 'not-a-subject')
            ->set('message', 'Halo, saya ingin bertanya tentang sekolah.')
            ->call('submit')
            ->assertHasErrors(['subject']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    #[DataProvider('validationMessageLocales')]
    public function test_contact_form_validation_messages_follow_the_page_language(string $locale, string $expectedNameError): void
    {
        app()->setLocale($locale);

        $errors = Livewire::test(ContactForm::class)
            ->call('submit')
            ->errors();

        $this->assertSame($expectedNameError, $errors->first('name'));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function validationMessageLocales(): array
    {
        return [
            'English' => ['en', 'The Full Name field is required.'],
            'Indonesian' => ['id', 'Nama Lengkap wajib diisi.'],
            'Traditional Chinese' => ['zh-TW', '中文或英文姓名為必填欄位。'],
        ];
    }

    public function test_rate_limited_visitor_sees_a_translated_message_and_nothing_is_stored(): void
    {
        foreach (range(1, 5) as $attempt) {
            RateLimiter::hit('contact-form:127.0.0.1', 600);
        }

        $component = Livewire::test(ContactForm::class)
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi@example.com')
            ->set('phone', '08123456789')
            ->set('subject', 'general')
            ->set('message', 'Halo, saya ingin bertanya tentang sekolah.')
            ->call('submit')
            ->assertSet('isSuccess', false);

        $this->assertStringStartsWith('Too many messages were sent.', $component->get('rateLimitError'));
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_honeypot_submission_looks_successful_but_is_not_stored(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'Spam Bot')
            ->set('email', 'bot@example.com')
            ->set('phone', '08123456789')
            ->set('subject', 'general')
            ->set('message', 'Buy cheap followers now at our website.')
            ->set('website', 'https://spam.example.com')
            ->call('submit')
            ->assertSet('isSuccess', true)
            ->assertSet('website', '');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_page_uses_subject_options_from_the_model(): void
    {
        $response = $this->get('/contact')->assertOk();

        foreach (ContactMessage::SUBJECTS as $subject) {
            $response->assertSee('value="'.$subject.'"', false);
        }

        $response->assertSeeText('Short-Term Mandarin Program / TOCFL')
            ->assertDontSee('pages.contact.', false);
    }

    public function test_submitted_and_updated_contact_messages_are_audited(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi@example.com')
            ->set('phone', '08123456789')
            ->set('subject', 'general')
            ->set('message', 'Halo, saya ingin bertanya tentang sekolah.')
            ->call('submit');

        $message = ContactMessage::sole();
        $message->update(['status' => ContactMessage::STATUS_REPLIED]);

        $this->assertDatabaseHas('audit_logs', ['entity_type' => 'ContactMessage', 'entity_id' => $message->id, 'action' => 'created']);
        $this->assertDatabaseHas('audit_logs', ['entity_type' => 'ContactMessage', 'entity_id' => $message->id, 'action' => 'updated']);
    }

    public function test_submitted_message_is_emailed_to_the_school_with_reply_to_the_sender(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi@example.com')
            ->set('phone', '08123456789')
            ->set('subject', 'admissions')
            ->set('message', 'Halo, saya ingin bertanya tentang pendaftaran.')
            ->call('submit')
            ->assertSet('isSuccess', true);

        $message = ContactMessage::sole();

        Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) use ($message): bool {
            return $mail->contactMessage->is($message)
                && $mail->hasTo('jtis.org@gmail.com')
                && $mail->hasReplyTo('budi@example.com', 'Budi Santoso');
        });
    }

    public function test_honeypot_submission_does_not_send_an_email(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Spam Bot')
            ->set('email', 'bot@example.com')
            ->set('phone', '08123456789')
            ->set('subject', 'general')
            ->set('message', 'Buy cheap followers now at our website.')
            ->set('website', 'https://spam.example.com')
            ->call('submit');

        Mail::assertNothingSent();
    }

    public function test_message_is_kept_and_visitor_sees_success_when_email_delivery_fails(): void
    {
        Exceptions::fake();
        Mail::shouldReceive('to')->andThrow(new TransportException('Resend is unavailable'));

        Livewire::test(ContactForm::class)
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi@example.com')
            ->set('phone', '08123456789')
            ->set('subject', 'general')
            ->set('message', 'Halo, saya ingin bertanya tentang sekolah.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSuccess', true);

        $this->assertDatabaseHas('contact_messages', ['email' => 'budi@example.com']);
        Exceptions::assertReported(TransportException::class);
    }

    public function test_contact_email_shows_the_message_details_and_escapes_visitor_input(): void
    {
        $message = ContactMessage::factory()->create([
            'name' => '<b>Budi</b> Santoso',
            'email' => 'budi@example.com',
            'phone' => '08123456789',
            'subject' => ContactMessage::SUBJECT_ADMISSIONS,
            'message' => "Baris pertama\n<script>alert(1)</script>",
        ]);

        $mailable = new ContactMessageReceived($message);

        $mailable->assertHasSubject('[JTS Website] New message: New Student Admissions - <b>Budi</b> Santoso');
        $mailable->assertSeeInHtml('&lt;b&gt;Budi&lt;/b&gt; Santoso', false);
        $mailable->assertDontSeeInHtml('<script>alert(1)</script>', false);
        $mailable->assertSeeInHtml('08123456789');
        $mailable->assertSeeInHtml(ContactMessageResource::getUrl('edit', ['record' => $message], panel: 'admin'), false);
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
