<?php

namespace Tests\Feature;

use App\Filament\Resources\AdmissionForms\AdmissionFormResource;
use App\Filament\Resources\AdmissionForms\Pages\CreateAdmissionForm;
use App\Filament\Resources\AdmissionForms\Pages\EditAdmissionForm;
use App\Models\AdmissionForm;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdmissionFormSeeder;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdmissionFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferenceDataSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_migration_preserves_existing_forms_and_classifies_them_as_wni(): void
    {
        Storage::fake('public');
        $migration = require database_path('migrations/2026_10_23_134203_add_nationality_to_admission_forms_table.php');
        $migration->down();

        Storage::disk('public')->put('admission-forms/original.pdf', 'original registration form');
        $id = DB::table('admission_forms')->insertGetId([
            'school_level' => 'tk',
            'title' => 'Original form',
            'file_path' => 'admission-forms/original.pdf',
            'is_active' => true,
        ]);

        $migration->up();

        $this->assertDatabaseHas('admission_forms', [
            'id' => $id,
            'nationality' => 'wni',
            'title' => 'Original form',
            'file_path' => 'admission-forms/original.pdf',
            'is_active' => true,
        ]);
        $this->assertSame($id, AdmissionForm::current()->id);
        $this->assertSame('original registration form', Storage::disk('public')->get('admission-forms/original.pdf'));
    }

    #[DataProvider('schoolLevels')]
    public function test_admissions_can_upload_a_separate_wna_form_for_each_school_level(string $schoolLevel): void
    {
        Storage::fake('public');
        $wniForm = $this->createForm($schoolLevel, 'wni');
        $this->actingAs($this->createUser('admissions'));

        Livewire::test(CreateAdmissionForm::class)
            ->fillForm([
                'title' => "Registration {$schoolLevel} WNA",
                'school_level' => $schoolLevel,
                'nationality' => 'wna',
                'file_path' => UploadedFile::fake()->create("{$schoolLevel}-wna.pdf", 10, 'application/pdf'),
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(AdmissionFormResource::getUrl('index'));

        $wnaForm = AdmissionForm::where('school_level', $schoolLevel)->where('nationality', 'wna')->firstOrFail();
        Storage::disk('public')->assertExists($wnaForm->file_path);
        $this->assertDatabaseHas('admission_forms', [
            'id' => $wnaForm->id,
            'title' => "Registration {$schoolLevel} WNA",
            'school_level' => $schoolLevel,
            'nationality' => 'wna',
        ]);
        $this->assertSame($wniForm->id, AdmissionForm::current($schoolLevel, 'wni')->id);
        $this->assertSame($wnaForm->id, AdmissionForm::current($schoolLevel, 'wna')->id);
        $this->assertDatabaseHas('admission_forms', ['id' => $wniForm->id, 'file_path' => $wniForm->file_path]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function schoolLevels(): array
    {
        return [
            'TK' => ['tk'],
            'SD' => ['sd'],
            'SMP' => ['smp'],
            'SMA' => ['sma'],
            'International' => ['international'],
        ];
    }

    public function test_wna_form_can_be_updated_independently_and_cannot_replace_the_wni_slot(): void
    {
        Storage::fake('public');
        $wniForm = $this->createForm('tk', 'wni');
        $wnaForm = $this->createForm('tk', 'wna');
        $this->actingAs($this->createUser('super_admin'));

        $editor = Livewire::test(EditAdmissionForm::class, ['record' => $wnaForm->id])
            ->fillForm([
                'title' => 'Updated WNA form',
                'file_path' => UploadedFile::fake()->create('updated-wna.pdf', 10, 'application/pdf'),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $wnaForm->refresh();
        $this->assertDatabaseHas('admission_forms', ['id' => $wnaForm->id, 'title' => 'Updated WNA form', 'nationality' => 'wna']);
        Storage::disk('public')->assertExists($wnaForm->file_path);
        $this->assertDatabaseHas('admission_forms', ['id' => $wniForm->id, 'file_path' => $wniForm->file_path]);

        $editor->fillForm(['nationality' => 'wni'])->call('save')
            ->assertHasFormErrors(['school_level' => 'unique']);
        $this->assertDatabaseHas('admission_forms', ['id' => $wnaForm->id, 'nationality' => 'wna']);
    }

    public function test_invalid_citizenship_is_rejected_when_creating_a_form(): void
    {
        Storage::fake('public');
        $this->actingAs($this->createUser('admissions'));

        Livewire::test(CreateAdmissionForm::class)
            ->fillForm([
                'title' => 'Invalid form',
                'school_level' => 'sd',
                'nationality' => 'invalid',
                'file_path' => UploadedFile::fake()->create('invalid.pdf', 10, 'application/pdf'),
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['nationality']);

        $this->assertDatabaseMissing('admission_forms', ['title' => 'Invalid form']);
    }

    public function test_existing_wni_forms_do_not_hide_the_create_action_for_wna_forms(): void
    {
        Storage::fake('public');
        foreach (['tk', 'sd', 'smp', 'sma', 'international'] as $schoolLevel) {
            $this->createForm($schoolLevel, 'wni');
        }
        $this->actingAs($this->createUser('admissions'));

        $this->assertTrue(AdmissionFormResource::canCreate());
        Livewire::test(CreateAdmissionForm::class)
            ->assertFormSet(['school_level' => 'tk', 'nationality' => 'wna']);

        foreach (['tk', 'sd', 'smp', 'sma', 'international'] as $schoolLevel) {
            $this->createForm($schoolLevel, 'wna');
        }

        $this->assertFalse(AdmissionFormResource::canCreate());
        Livewire::test(CreateAdmissionForm::class)->assertForbidden();
    }

    public function test_academic_users_cannot_create_registration_forms(): void
    {
        $this->actingAs($this->createUser('academic'));

        Livewire::test(CreateAdmissionForm::class)->assertForbidden();
    }

    #[DataProvider('publicLocales')]
    public function test_public_admissions_provides_both_forms_per_level_in_each_language(
        string $prefix,
        string $wniLabel,
        string $wnaLabel,
    ): void {
        Storage::fake('public');
        foreach (['tk', 'sd', 'smp', 'sma', 'international'] as $schoolLevel) {
            $this->createForm($schoolLevel, 'wni');
            $this->createForm($schoolLevel, 'wna');
        }

        $response = $this->get($prefix.'/admissions')->assertOk()
            ->assertSeeText($wniLabel)
            ->assertSeeText($wnaLabel)
            ->assertDontSee('site.download_admission_form_', false);

        foreach (['tk', 'sd', 'smp', 'sma', 'international'] as $schoolLevel) {
            $response->assertSee("{$schoolLevel}-wni.pdf", false)
                ->assertSee("{$schoolLevel}-wna.pdf", false);
        }
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function publicLocales(): array
    {
        return [
            'English' => ['', 'Download registration form WNI', 'Download registration form WNA'],
            'Indonesian' => ['/id', 'Unduh form pendaftaran WNI', 'Unduh form pendaftaran WNA'],
            'Traditional Chinese' => ['/zh', '下載印尼籍（WNI）報名表', '下載外籍（WNA）報名表'],
        ];
    }

    public function test_inactive_missing_and_unsafe_wna_files_are_not_offered_or_replaced_with_wni_files(): void
    {
        Storage::fake('public');
        $wniForm = $this->createForm('tk', 'wni');
        $inactiveForm = $this->createForm('tk', 'wna');
        $inactiveForm->update(['is_active' => false]);
        $missingForm = $this->createForm('sd', 'wna');
        Storage::disk('public')->delete($missingForm->file_path);
        $unsafeForm = $this->createForm('smp', 'wna');
        $unsafeForm->update(['file_path' => '../unsafe-wna.pdf']);

        $this->assertTrue(AdmissionForm::downloadableByLevel('wna')->isEmpty());
        $this->assertNull(AdmissionForm::current('tk', 'wna'));
        $this->get('/admissions')->assertOk()
            ->assertSee('tk-wni.pdf', false)
            ->assertDontSee('tk-wna.pdf', false)
            ->assertDontSee('sd-wna.pdf', false)
            ->assertDontSee('unsafe-wna.pdf', false);
        $this->get('/download/admission-form')->assertOk()->assertDownload($wniForm->download_name);
    }

    public function test_seeders_preserve_uploaded_wna_forms_when_adding_default_wni_forms(): void
    {
        Storage::fake('public');
        $wnaForm = $this->createForm('sd', 'wna');

        $this->seed(AdmissionFormSeeder::class);
        $this->seed(AdmissionFormSeeder::class);

        $this->assertDatabaseCount('admission_forms', 6);
        $this->assertDatabaseHas('admission_forms', ['id' => $wnaForm->id, 'file_path' => $wnaForm->file_path, 'nationality' => 'wna']);
        $this->assertDatabaseHas('admission_forms', ['school_level' => 'sd', 'nationality' => 'wni']);
    }

    private function createUser(string $role): User
    {
        return User::factory()->create([
            'role_id' => Role::where('code', $role)->value('id'),
            'is_active' => true,
        ]);
    }

    private function createForm(string $schoolLevel, string $nationality): AdmissionForm
    {
        $path = "admission-forms/{$schoolLevel}-{$nationality}.pdf";
        Storage::disk('public')->put($path, '%PDF-1.4 registration '.$schoolLevel.' '.$nationality);

        return AdmissionForm::create([
            'school_level' => $schoolLevel,
            'nationality' => $nationality,
            'title' => "Form {$schoolLevel} {$nationality}",
            'file_path' => $path,
            'is_active' => true,
        ]);
    }
}
