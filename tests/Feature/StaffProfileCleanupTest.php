<?php

namespace Tests\Feature;

use App\Filament\Resources\StaffProfiles\Pages\CreateStaffProfile;
use App\Filament\Resources\StaffProfiles\Pages\EditStaffProfile;
use App\Models\Department;
use App\Models\StaffProfile;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class StaffProfileCleanupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        StaffProfile::query()->delete();
        $this->seed(ReferenceDataSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admin_can_create_and_edit_staff_without_removed_fields(): void
    {
        $this->actingAs(User::where('email', 'admin@jts.local')->firstOrFail());

        $this->get('/jts-secure-portal/staff-profiles/create')
            ->assertOk()
            ->assertDontSeeText('Nomor Ekstensi Telp')
            ->assertDontSeeText('Tanggung Jawab & Peran')
            ->assertDontSeeText('Rincian Tugas');

        Livewire::test(CreateStaffProfile::class)
            ->fillForm([
                'name' => 'Integration Teacher',
                'title' => 'Teacher',
                'department_id' => Department::firstOrFail()->id,
                'sort_order' => 0,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $staff = StaffProfile::where('name', 'Integration Teacher')->firstOrFail();

        $this->get('/jts-secure-portal/staff-profiles')
            ->assertOk()
            ->assertSeeText('Integration Teacher')
            ->assertDontSeeText('Ekstensi');
        $this->get('/jts-secure-portal/staff-profiles/'.$staff->id.'/edit')
            ->assertOk()
            ->assertDontSeeText('Nomor Ekstensi Telp')
            ->assertDontSeeText('Rincian Tugas');

        Livewire::test(EditStaffProfile::class, ['record' => $staff->id])
            ->fillForm(['title' => 'Senior Teacher'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Senior Teacher', $staff->fresh()->title);
    }

    public function test_public_staff_pages_keep_core_profile_information_in_every_locale(): void
    {
        $department = Department::firstOrFail();
        StaffProfile::create([
            'name' => 'Integration Teacher',
            'title' => 'Senior Teacher',
            'department_id' => $department->id,
            'is_active' => true,
        ]);

        foreach (['' => 'Extension', '/id' => 'Ekstensi', '/zh' => '分機'] as $prefix => $removedLabel) {
            $this->get($prefix.'/staff?department='.$department->id)
                ->assertOk()
                ->assertSeeText('Integration Teacher')
                ->assertSeeText('Senior Teacher')
                ->assertSee('images/jtsidlogo.svg', false)
                ->assertDontSee('<select name="department"', false)
                ->assertDontSeeText($removedLabel);
        }
    }

    public function test_migration_drops_both_columns_without_deleting_staff_and_rollback_restores_empty_columns(): void
    {
        $staff = StaffProfile::create([
            'name' => 'Migration Teacher',
            'title' => 'Teacher',
            'department_id' => Department::firstOrFail()->id,
        ]);

        $this->assertFalse(Schema::hasColumn('staff_profiles', 'extension'));
        $this->assertFalse(Schema::hasColumn('staff_profiles', 'responsibilities'));

        $migration = require database_path('migrations/2026_09_24_155756_remove_extension_and_responsibilities_from_staff_profiles_table.php');
        $migration->down();

        $this->assertTrue(Schema::hasColumn('staff_profiles', 'extension'));
        $this->assertTrue(Schema::hasColumn('staff_profiles', 'responsibilities'));
        $this->assertNull(DB::table('staff_profiles')->where('id', $staff->id)->value('extension'));
        $this->assertNull(DB::table('staff_profiles')->where('id', $staff->id)->value('responsibilities'));

        DB::table('staff_profiles')->where('id', $staff->id)->update([
            'extension' => '101',
            'responsibilities' => 'Legacy duties',
        ]);
        $migration->up();

        $this->assertFalse(Schema::hasColumn('staff_profiles', 'extension'));
        $this->assertFalse(Schema::hasColumn('staff_profiles', 'responsibilities'));
        $this->assertDatabaseHas('staff_profiles', ['id' => $staff->id, 'name' => 'Migration Teacher']);

        $migration->down();
        $this->assertNull(DB::table('staff_profiles')->where('id', $staff->id)->value('extension'));
        $this->assertNull(DB::table('staff_profiles')->where('id', $staff->id)->value('responsibilities'));
        $migration->up();

        $this->artisan('migrate:fresh');
    }
}
