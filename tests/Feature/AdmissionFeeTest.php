<?php

namespace Tests\Feature;

use App\Filament\Resources\AdmissionFees\AdmissionFeeResource;
use App\Filament\Resources\AdmissionFees\Pages\CreateAdmissionFee;
use App\Models\AdmissionFee;
use App\Models\GradeLevel;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdmissionFeeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferenceDataSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admissions_page_displays_fee_structure_with_localization_and_notes(): void
    {
        $tk = GradeLevel::where('code', 'tk')->firstOrFail();

        AdmissionFee::factory()->create([
            'grade_level_id' => $tk->id,
            'name' => ['en' => 'Registration Fee', 'id' => 'Uang Pendaftaran', 'zh-TW' => '報名費'],
            'amount' => 'Rp. 1.000.000,-',
            'payment_period' => ['en' => 'one-time payment', 'id' => 'satu kali pembayaran', 'zh-TW' => '一次性繳納'],
            'note' => null,
            'is_active' => true,
        ]);

        AdmissionFee::factory()->create([
            'grade_level_id' => $tk->id,
            'name' => ['en' => 'Development Fee', 'id' => 'Uang Pembangunan', 'zh-TW' => '建設發展費'],
            'amount' => 'Rp. 5.000.000,-',
            'payment_period' => ['en' => 'one-time payment', 'id' => 'satu kali pembayaran', 'zh-TW' => '一次性繳納'],
            'note' => ['en' => 'For second child Rp. 3,500,000,-', 'id' => 'Untuk anak kedua Rp. 3.500.000,-', 'zh-TW' => '第二位子女 Rp. 3,500,000,-'],
            'is_active' => true,
        ]);

        // Test Indonesian
        $this->get('/id/admissions')
            ->assertOk()
            ->assertSeeText('Rincian Biaya Sekolah')
            ->assertSeeText('Taman Kanak-Kanak')
            ->assertDontSeeText('Siswa Internasional')
            ->assertSeeText('Uang Pendaftaran')
            ->assertSeeText('Rp. 1.000.000,-')
            ->assertSeeText('satu kali pembayaran')
            ->assertSeeText('Uang Pembangunan')
            ->assertSeeText('Rp. 5.000.000,-')
            ->assertSeeText('Untuk anak kedua Rp. 3.500.000,-');

        // Test English
        $this->get('/admissions')
            ->assertOk()
            ->assertSeeText('Tuition & Fee Structure')
            ->assertSeeText('Kindergarten')
            ->assertDontSeeText('International Student')
            ->assertSeeText('Registration Fee')
            ->assertSeeText('Rp. 1.000.000,-')
            ->assertSeeText('one-time payment')
            ->assertSeeText('Development Fee')
            ->assertSeeText('For second child Rp. 3,500,000,-');

        // Test Chinese
        $this->get('/zh/admissions')
            ->assertOk()
            ->assertSeeText('各年級收費標準')
            ->assertSeeText('幼兒園')
            ->assertDontSeeText('國際學生')
            ->assertSeeText('報名費')
            ->assertSeeText('建設發展費')
            ->assertSeeText('第二位子女 Rp. 3,500,000,-');
    }

    public function test_inactive_fees_are_not_displayed_on_public_page(): void
    {
        $tk = GradeLevel::where('code', 'tk')->firstOrFail();

        AdmissionFee::factory()->create([
            'grade_level_id' => $tk->id,
            'name' => ['en' => 'Secret Hidden Fee', 'id' => 'Biaya Rahasia', 'zh-TW' => '隱藏費用'],
            'is_active' => false,
        ]);

        $this->get('/id/admissions')
            ->assertOk()
            ->assertDontSeeText('Biaya Rahasia');
    }

    public function test_admissions_page_shows_fee_attachment_download_below_the_note(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('documents/fee-details.pdf', 'PDF');
        $sd = GradeLevel::where('code', 'sd')->firstOrFail();

        $withFile = AdmissionFee::factory()->create([
            'grade_level_id' => $sd->id,
            'note' => ['en' => '20% discount for second child', 'id' => 'Diskon 20% anak kedua', 'zh-TW' => '第二位子女八折'],
            'file_path' => 'documents/fee-details.pdf',
            'file_label' => ['en' => 'Discount terms', 'id' => 'Ketentuan diskon', 'zh-TW' => '優惠說明'],
        ]);
        $withoutLabel = AdmissionFee::factory()->create([
            'grade_level_id' => $sd->id,
            'file_path' => 'documents/fee-details.pdf',
        ]);
        $withoutFile = AdmissionFee::factory()->create(['grade_level_id' => $sd->id]);

        $html = $this->get('/admissions')->assertOk()
            ->assertSeeInOrder(['20% discount for second child', 'Discount terms'])
            ->assertSee('href="/download/admission-fee/'.$withFile->id.'"', false)
            ->assertSee('href="/download/admission-fee/'.$withoutLabel->id.'"', false)
            ->assertDontSee('href="/download/admission-fee/'.$withoutFile->id.'"', false)
            ->getContent();
        $this->assertStringContainsString('Download file', $html);

        $this->get('/zh/admissions')->assertOk()
            ->assertSeeText('優惠說明')
            ->assertSee('href="/zh/download/admission-fee/'.$withFile->id.'"', false);
    }

    public function test_fee_attachment_downloads_with_the_original_uploaded_file_name(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('documents/01JABCRANDOM.pdf', 'PDF');
        $fee = AdmissionFee::factory()->create([
            'file_path' => 'documents/01JABCRANDOM.pdf',
            'file_name' => 'Rincian Biaya SD 2026.pdf',
        ]);

        $response = $this->get(route('en.download.admission-fee', $fee))->assertOk();

        $this->assertStringContainsString('Rincian Biaya SD 2026.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_fee_attachment_without_a_stored_name_falls_back_to_the_fee_name(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('documents/fee-details.pdf', 'PDF');
        $fee = AdmissionFee::factory()->create([
            'name' => ['en' => 'Development Fee', 'id' => 'Uang Pembangunan', 'zh-TW' => '建設發展費'],
            'file_path' => 'documents/fee-details.pdf',
            'file_name' => null,
        ]);

        $response = $this->get(route('en.download.admission-fee', $fee))->assertOk();

        $this->assertStringContainsString('development-fee.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_fee_attachment_is_not_downloadable_when_inactive_missing_or_outside_documents(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('documents/fee-details.pdf', 'PDF');
        Storage::disk('local')->put('private/secret.pdf', 'SECRET');

        $inactive = AdmissionFee::factory()->create(['file_path' => 'documents/fee-details.pdf', 'is_active' => false]);
        $missingFile = AdmissionFee::factory()->create(['file_path' => 'documents/deleted.pdf']);
        $noFile = AdmissionFee::factory()->create(['file_path' => null]);
        $outsideDocuments = AdmissionFee::factory()->create(['file_path' => 'private/secret.pdf']);

        foreach ([$inactive, $missingFile, $noFile, $outsideDocuments] as $fee) {
            $this->get(route('en.download.admission-fee', $fee))->assertNotFound();
        }
    }

    public function test_admin_can_upload_a_fee_attachment_from_the_cms(): void
    {
        Storage::fake('local');
        $this->actingAs(User::where('email', 'admin@jts.local')->firstOrFail());

        Livewire::test(CreateAdmissionFee::class)
            ->fillForm([
                'grade_level_id' => GradeLevel::where('code', 'sd')->value('id'),
                'amount' => 'Rp. 6.000.000,-',
                'name.id' => 'Uang Pembangunan',
                'payment_period.id' => 'per semester',
                'file_label.en' => 'Discount terms',
                'file_path' => UploadedFile::fake()->create('rincian.pdf', 20, 'application/pdf'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $fee = AdmissionFee::sole();

        $this->assertStringStartsWith('documents/', $fee->file_path);
        $this->assertSame('rincian.pdf', $fee->file_name);
        Storage::disk('local')->assertExists($fee->file_path);
        $this->assertSame('Discount terms', $fee->fileLabel('en'));
    }

    public function test_admin_can_access_admission_fees_cms_resource(): void
    {
        $tk = GradeLevel::where('code', 'tk')->firstOrFail();
        $sd = GradeLevel::where('code', 'sd')->firstOrFail();
        $smp = GradeLevel::where('code', 'smp')->firstOrFail();
        $sma = GradeLevel::where('code', 'sma')->firstOrFail();
        $intl = GradeLevel::where('code', 'international')->firstOrFail();

        AdmissionFee::factory()->create(['grade_level_id' => $tk->id]);
        AdmissionFee::factory()->create(['grade_level_id' => $sd->id]);
        AdmissionFee::factory()->create(['grade_level_id' => $smp->id]);
        AdmissionFee::factory()->create(['grade_level_id' => $sma->id]);
        AdmissionFee::factory()->create(['grade_level_id' => $intl->id]);
        $admin = User::where('email', 'admin@jts.local')->firstOrFail();
        $this->actingAs($admin)->withSession(['admin_locale' => 'id']);

        $this->get(AdmissionFeeResource::getUrl('index'))
            ->assertOk()
            ->assertDontSeeText('Semua Jenjang')
            ->assertSeeText('Taman Kanak-Kanak (TK)')
            ->assertSeeText('Sekolah Dasar (SD)')
            ->assertSeeText('Sekolah Menengah Pertama (SMP)')
            ->assertSeeText('Sekolah Menengah Atas (SMA)')
            ->assertSeeText('Siswa Internasional');

        $this->get(AdmissionFeeResource::getUrl('index', ['tab' => 'smp']))
            ->assertOk();

        $this->get(AdmissionFeeResource::getUrl('index', ['tab' => 'international']))
            ->assertOk();

        $this->get(AdmissionFeeResource::getUrl('create', ['grade_level' => 'international']))
            ->assertOk();
    }
}
