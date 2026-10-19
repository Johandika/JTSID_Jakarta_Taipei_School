<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\GradeLevel;
use App\Models\PostCategory;
use App\Models\Role;
use App\Models\SubjectResourceType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'super_admin' => 'Super Admin',
            'admissions' => 'Pendaftaran',
            'academic' => 'Academic',
            'student_affairs' => 'Student Affair',
            'clc' => 'Chinese Learning Center',
            'kindergarten' => 'Kindergarten',
            'secretary' => 'Secretary',
            'languages' => 'Language Department',
            'library' => 'Library',
            'content_admin' => 'Content Admin',
            'department_admin' => 'Department Admin',
            'viewer' => 'Viewer',
        ] as $code => $name) {
            Role::firstOrCreate(compact('code'), compact('name'));
        }
        foreach (['secretary' => 'Secretary Office', 'academic' => 'Academic Office', 'student_affairs' => 'Student Affairs', 'languages' => 'International Languages', 'kindergarten' => 'Kindergarten', 'clc' => 'Chinese Learning Center', 'general_affairs' => 'General Affairs', 'accounting' => 'Accounting', 'hr' => 'Human Resources', 'admissions' => 'Admissions', 'library' => 'Library'] as $code => $name) {
            Department::firstOrCreate(compact('code'), compact('name'));
        }
        foreach (['news' => 'News', 'media' => 'Media', 'activity' => 'Campus Activities', 'exchange' => 'International Exchange', 'newsletter' => 'Newsletter', 'admission_notice' => 'Admission Notices', 'achievement' => 'Student Achievement', 'announcement' => 'Announcements'] as $code => $name) {
            PostCategory::firstOrCreate(compact('code'), compact('name'));
        }
        foreach (['tk' => 'TK', 'sd' => 'SD', 'smp' => 'SMP', 'sma' => 'SMA', 'international' => 'International Student'] as $code => $name) {
            GradeLevel::firstOrCreate(compact('code'), compact('name'));
        }
        foreach (['calendar' => 'Calendar', 'classroom_regulations' => 'Classroom Regulations', 'syllabus' => 'Syllabus', 'textbook' => 'Textbook', 'lesson_progress' => 'Lesson Progress', 'weekly_news' => 'Weekly News'] as $code => $name) {
            SubjectResourceType::firstOrCreate(compact('code'), compact('name'));
        }
        foreach (['2025-2026', '2026-2027'] as $year) {
            AcademicYear::firstOrCreate(['code' => $year], ['name' => str_replace('-', ' / ', $year)]);
        }
        $this->call(InitialAdminSeeder::class);

        if (app()->environment('testing')) {
            foreach (['editor' => 'content_admin', 'academic' => 'department_admin', 'viewer' => 'viewer'] as $username => $code) {
                User::firstOrCreate(['email' => $username.'@jts.local'], [
                    'name' => Role::where('code', $code)->value('name'),
                    'password' => 'JtsTest2026!',
                    'role_id' => Role::where('code', $code)->value('id'),
                    'department_id' => $code === 'department_admin' ? Department::where('code', 'academic')->value('id') : null,
                    'is_active' => true,
                ]);
            }
        }
    }
}
