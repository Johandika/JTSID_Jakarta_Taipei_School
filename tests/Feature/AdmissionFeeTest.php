<?php

namespace Tests\Feature;

use App\Filament\Resources\AdmissionFees\AdmissionFeeResource;
use App\Models\AdmissionFee;
use App\Models\GradeLevel;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_admin_can_access_admission_fees_cms_resource(): void
    {
        $tk = GradeLevel::where('code', 'tk')->firstOrFail();
        $sd = GradeLevel::where('code', 'sd')->firstOrFail();
        $smp = GradeLevel::where('code', 'smp')->firstOrFail();
        $sma = GradeLevel::where('code', 'sma')->firstOrFail();

        AdmissionFee::factory()->create(['grade_level_id' => $tk->id]);
        AdmissionFee::factory()->create(['grade_level_id' => $sd->id]);
        AdmissionFee::factory()->create(['grade_level_id' => $smp->id]);
        AdmissionFee::factory()->create(['grade_level_id' => $sma->id]);
        $admin = User::where('email', 'admin@jts.local')->firstOrFail();
        $this->actingAs($admin)->withSession(['admin_locale' => 'id']);

        $this->get(AdmissionFeeResource::getUrl('index'))
            ->assertOk()
            ->assertDontSeeText('Semua Jenjang')
            ->assertSeeText('Taman Kanak-Kanak (TK)')
            ->assertSeeText('Sekolah Dasar (SD)')
            ->assertSeeText('Sekolah Menengah Pertama (SMP)')
            ->assertSeeText('Sekolah Menengah Atas (SMA)');

        $this->get(AdmissionFeeResource::getUrl('index', ['tab' => 'smp']))
            ->assertOk();

        $this->get(AdmissionFeeResource::getUrl('create', ['grade_level' => 'smp']))
            ->assertOk();
    }
}
