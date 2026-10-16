<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class InitialAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (User::exists()) {
            return;
        }

        $password = config('site.initial_admin.password');

        if (blank($password)) {
            throw new RuntimeException('Set LOCAL_ADMIN_PASSWORD before seeding the initial administrator.');
        }

        $role = Role::firstOrCreate(['code' => 'super_admin'], ['name' => 'Super Admin']);

        User::create([
            'name' => config('site.initial_admin.name'),
            'email' => config('site.initial_admin.email'),
            'password' => $password,
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}
