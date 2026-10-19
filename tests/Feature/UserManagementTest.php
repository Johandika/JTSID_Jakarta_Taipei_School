<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\InitialAdminSeeder;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[DataProvider('seedEnvironments')]
    public function test_seeding_creates_only_the_initial_super_admin_outside_testing(string $environment): void
    {
        try {
            app()->instance('env', $environment);
            config()->set('site.initial_admin', [
                'name' => 'Initial Administrator',
                'email' => 'initial@example.com',
                'password' => 'InitialPassword2026!',
            ]);

            $this->artisan('db:seed', ['--class' => ReferenceDataSeeder::class, '--force' => true])
                ->assertExitCode(0);

            $this->assertDatabaseCount('users', 1);
            $admin = User::sole();
            $this->assertSame('Initial Administrator', $admin->name);
            $this->assertSame('initial@example.com', $admin->email);
            $this->assertSame('super_admin', $admin->role->code);
            $this->assertTrue($admin->is_active);
            $this->assertTrue(Hash::check('InitialPassword2026!', $admin->password));
        } finally {
            app()->instance('env', 'testing');
            $this->artisan('migrate:fresh');
        }
    }

    public static function seedEnvironments(): array
    {
        return ['local' => ['local'], 'production' => ['production']];
    }

    public function test_reseeding_does_not_reset_or_recreate_an_edited_initial_account(): void
    {
        config()->set('site.initial_admin', [
            'name' => 'Initial Administrator',
            'email' => 'initial@example.com',
            'password' => 'InitialPassword2026!',
        ]);
        $this->seed(InitialAdminSeeder::class);
        User::sole()->update([
            'name' => 'Updated Administrator',
            'email' => 'updated@example.com',
            'password' => 'UpdatedPassword2026!',
            'is_active' => false,
        ]);
        config()->set('site.initial_admin.password', null);

        $this->seed(InitialAdminSeeder::class);

        $this->assertDatabaseCount('users', 1);
        $admin = User::sole();
        $this->assertSame('Updated Administrator', $admin->name);
        $this->assertSame('updated@example.com', $admin->email);
        $this->assertFalse($admin->is_active);
        $this->assertTrue(Hash::check('UpdatedPassword2026!', $admin->password));
    }

    public function test_empty_initial_password_is_rejected(): void
    {
        config()->set('site.initial_admin.password', null);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Set LOCAL_ADMIN_PASSWORD');

        $this->seed(InitialAdminSeeder::class);
    }

    public function test_admin_creates_a_super_admin_who_can_login_using_database_credentials(): void
    {
        $this->actingAs($this->createAdministrator());
        $superAdminRole = Role::firstOrCreate(['code' => 'super_admin'], ['name' => 'Super Admin']);

        Livewire::test(CreateUser::class)
            ->fillForm($this->validAccountData())
            ->set('data.role_id', $superAdminRole->id)
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(UserResource::getUrl('index'));

        $user = User::where('email', 'new@example.com')->sole();
        $this->assertSame('New Administrator', $user->name);
        $this->assertSame('super_admin', $user->role->code);
        $this->assertNull($user->department_id);
        $this->assertTrue(Hash::check('NewPassword2026!', $user->password));
        Filament::auth()->logout();
        config()->set('site.initial_admin.password', 'DifferentSeedPassword!');

        Livewire::test(Login::class)
            ->fillForm(['email' => 'new@example.com', 'password' => 'NewPassword2026!'])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_create_user_with_division_role_and_department_is_auto_assigned(): void
    {
        $this->actingAs($this->createAdministrator());
        $admissionsRole = Role::firstOrCreate(['code' => 'admissions'], ['name' => 'Pendaftaran']);
        $department = Department::firstOrCreate(['code' => 'admissions'], ['name' => 'Admissions']);

        Livewire::test(CreateUser::class)
            ->fillForm($this->validAccountData())
            ->set('data.role_id', $admissionsRole->id)
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(UserResource::getUrl('index'));

        $user = User::where('email', 'new@example.com')->sole();
        $this->assertSame('admissions', $user->role->code);
        $this->assertSame($department->id, $user->department_id);
    }

    #[DataProvider('invalidAccounts')]
    public function test_invalid_account_data_is_rejected_without_creating_a_user(array $changes, array $errors): void
    {
        $this->actingAs($this->createAdministrator());

        Livewire::test(CreateUser::class)
            ->fillForm(array_replace($this->validAccountData(), $changes))
            ->call('create')
            ->assertHasFormErrors($errors);

        $this->assertDatabaseCount('users', 1);
    }

    public static function invalidAccounts(): array
    {
        return [
            'required fields' => [['name' => '', 'email' => '', 'password' => '', 'password_confirmation' => ''], ['name' => 'required', 'email' => 'required', 'password' => 'required']],
            'invalid email' => [['email' => 'invalid-email'], ['email' => 'email']],
            'duplicate email' => [['email' => 'admin@example.com'], ['email' => 'unique']],
            'short password' => [['password' => 'short', 'password_confirmation' => 'short'], ['password' => 'min']],
            'long password' => [['password' => str_repeat('a', 73), 'password_confirmation' => str_repeat('a', 73)], ['password' => 'max']],
            'mismatched confirmation' => [['password_confirmation' => 'DifferentPassword!'], ['password' => 'same']],
            'missing confirmation' => [['password_confirmation' => ''], ['password_confirmation' => 'required_with']],
        ];
    }

    public function test_editing_account_details_with_blank_password_preserves_the_password(): void
    {
        $this->actingAs($this->createAdministrator());
        $user = $this->createAdministrator(['email' => 'other@example.com']);
        $originalPassword = $user->password;

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->assertFormSet(['password' => null])
            ->fillForm(['name' => 'Edited Administrator', 'email' => 'edited@example.com', 'password' => '', 'password_confirmation' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Edited Administrator', $user->fresh()->name);
        $this->assertSame('edited@example.com', $user->fresh()->email);
        $this->assertSame($originalPassword, $user->fresh()->password);
    }

    public function test_changing_password_rejects_the_old_password_and_accepts_the_new_password(): void
    {
        $this->actingAs($this->createAdministrator());
        $user = $this->createAdministrator(['email' => 'other@example.com', 'password' => 'OldPassword2026!']);

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->fillForm(['email' => 'changed@example.com', 'password' => 'ChangedPassword2026!', 'password_confirmation' => 'ChangedPassword2026!'])
            ->call('save')
            ->assertHasNoFormErrors();

        Filament::auth()->logout();
        Livewire::test(Login::class)
            ->fillForm(['email' => 'changed@example.com', 'password' => 'OldPassword2026!'])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);
        $this->assertGuest();

        Livewire::test(Login::class)
            ->fillForm(['email' => 'changed@example.com', 'password' => 'ChangedPassword2026!'])
            ->call('authenticate')
            ->assertHasNoFormErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_change_their_own_password_without_losing_the_current_session(): void
    {
        $admin = $this->createAdministrator();
        $this->actingAs($admin)->withSession(['password_hash_web' => $admin->password]);

        Livewire::test(EditUser::class, ['record' => $admin->id])
            ->fillForm(['password' => 'ChangedPassword2026!', 'password_confirmation' => 'ChangedPassword2026!'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('ChangedPassword2026!', $admin->fresh()->password));
        $this->get(UserResource::getUrl('index'))->assertOk();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_cannot_deactivate_or_delete_their_own_account(): void
    {
        $admin = $this->createAdministrator();
        $this->actingAs($admin);

        Livewire::test(EditUser::class, ['record' => $admin->id])
            ->set('data.is_active', false)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertActionHidden(DeleteAction::class);

        $this->assertTrue($admin->fresh()->is_active);
        $this->assertModelExists($admin);
    }

    public function test_admin_can_deactivate_another_account_and_inactive_users_cannot_login(): void
    {
        $this->actingAs($this->createAdministrator());
        $user = $this->createAdministrator(['email' => 'other@example.com', 'password' => 'KnownPassword2026!']);

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->fillForm(['is_active' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertFalse($user->fresh()->is_active);
        Filament::auth()->logout();
        Livewire::test(Login::class)
            ->fillForm(['email' => 'other@example.com', 'password' => 'KnownPassword2026!'])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);
        $this->assertGuest();
    }

    public function test_admin_can_delete_another_user(): void
    {
        $this->actingAs($this->createAdministrator());
        $user = $this->createAdministrator(['email' => 'other@example.com']);

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->callAction(DeleteAction::class)
            ->assertRedirect(UserResource::getUrl('index'));

        $this->assertModelMissing($user);
    }

    public function test_guests_are_redirected_to_login_from_user_management(): void
    {
        $this->get(UserResource::getUrl('index'))->assertRedirect(Filament::getLoginUrl());
        $this->get(UserResource::getUrl('create'))->assertRedirect(Filament::getLoginUrl());
    }

    public function test_non_super_admins_cannot_access_user_management(): void
    {
        $admin = $this->createAdministrator();
        $viewer = User::factory()->create([
            'role_id' => Role::create(['code' => 'viewer', 'name' => 'Viewer'])->id,
            'is_active' => true,
        ]);
        $this->actingAs($viewer);

        $this->get(UserResource::getUrl('index'))->assertForbidden();
        $this->get(UserResource::getUrl('create'))->assertForbidden();
        $this->get(UserResource::getUrl('edit', ['record' => $admin]))->assertForbidden();
    }

    #[DataProvider('adminLanguages')]
    public function test_user_management_is_translated_in_all_admin_languages(string $locale, string $users, string $confirmation, string $editAccount, string $role): void
    {
        $admin = $this->createAdministrator();
        $this->actingAs($admin)->withSession(['admin_locale' => $locale]);

        $this->get(UserResource::getUrl('index'))->assertSeeText($users)->assertSeeText($role)->assertSeeText($editAccount);
        $this->get(UserResource::getUrl('create'))->assertSeeText($confirmation)->assertSeeText($role);
        $this->get(UserResource::getUrl('edit', ['record' => $admin]))->assertSeeText($confirmation);
    }

    public static function adminLanguages(): array
    {
        return [
            'English' => ['en', 'Users', 'Confirm password', 'Edit my account', 'Super Admin'],
            'Indonesian' => ['id', 'Pengguna', 'Konfirmasi kata sandi', 'Ubah akun saya', 'Super Admin'],
            'Traditional Chinese' => ['zh_TW', '使用者', '確認密碼', '編輯我的帳戶', '超級管理員'],
        ];
    }

    #[DataProvider('validationLanguages')]
    public function test_password_validation_messages_are_translated(string $locale, string $message): void
    {
        $this->actingAs($this->createAdministrator());
        app()->setLocale($locale);

        Livewire::test(CreateUser::class)
            ->fillForm(array_replace($this->validAccountData(), ['password_confirmation' => 'DifferentPassword!']))
            ->call('create')
            ->assertHasFormErrors(['password' => 'same'])
            ->assertSeeText($message);

        $this->assertDatabaseCount('users', 1);
    }

    public static function validationLanguages(): array
    {
        return [
            'English' => ['en', 'The passwords do not match.'],
            'Indonesian' => ['id', 'Kata sandi dan konfirmasi tidak cocok.'],
            'Traditional Chinese' => ['zh_TW', '密碼與確認密碼不一致。'],
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createAdministrator(array $attributes = []): User
    {
        return User::factory()->create(array_replace([
            'email' => 'admin@example.com',
            'role_id' => Role::firstOrCreate(['code' => 'super_admin'], ['name' => 'Super Admin'])->id,
            'is_active' => true,
        ], $attributes));
    }

    /**
     * @return array{name: string, email: string, password: string, password_confirmation: string, is_active: bool}
     */
    private function validAccountData(): array
    {
        return [
            'name' => 'New Administrator',
            'email' => 'new@example.com',
            'password' => 'NewPassword2026!',
            'password_confirmation' => 'NewPassword2026!',
            'is_active' => true,
        ];
    }
}
