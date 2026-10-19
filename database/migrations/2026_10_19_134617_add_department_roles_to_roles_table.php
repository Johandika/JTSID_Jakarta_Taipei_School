<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();
        $roles = [
            ['code' => 'admissions', 'name' => 'Pendaftaran'],
            ['code' => 'academic', 'name' => 'Academic'],
            ['code' => 'student_affairs', 'name' => 'Student Affair'],
            ['code' => 'clc', 'name' => 'Chinese Learning Center'],
            ['code' => 'kindergarten', 'name' => 'Kindergarten'],
            ['code' => 'secretary', 'name' => 'Secretary'],
            ['code' => 'languages', 'name' => 'Language Department'],
            ['code' => 'library', 'name' => 'Library'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore([
                'code' => $role['code'],
                'name' => $role['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->whereIn('code', [
            'admissions',
            'academic',
            'student_affairs',
            'clc',
            'kindergarten',
            'secretary',
            'languages',
            'library',
        ])->delete();
    }
};
