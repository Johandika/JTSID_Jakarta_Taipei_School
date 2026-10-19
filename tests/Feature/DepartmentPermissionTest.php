<?php

namespace Tests\Feature;

use App\Filament\Resources\AdmissionFees\AdmissionFeeResource;
use App\Filament\Resources\AdmissionForms\AdmissionFormResource;
use App\Filament\Resources\AnnouncementPopups\AnnouncementPopupResource;
use App\Filament\Resources\HeroSlides\HeroSlideResource;
use App\Filament\Resources\MandarinPrograms\MandarinProgramResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\SubjectResources\SubjectResourceResource;
use App\Models\AdmissionFee;
use App\Models\AdmissionForm;
use App\Models\AnnouncementPopup;
use App\Models\Department;
use App\Models\HeroSlide;
use App\Models\MandarinProgram;
use App\Models\Post;
use App\Models\Role;
use App\Models\SubjectResource;
use App\Models\User;
use App\Support\DepartmentPermission;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class DepartmentPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferenceDataSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_department_admins_can_only_access_their_allowed_models(): void
    {
        $role = Role::where('code', 'department_admin')->firstOrFail();

        // 1. Admissions / Pendaftaran
        $admissionsDept = Department::where('code', 'admissions')->firstOrFail();
        $admissionsUser = User::factory()->create([
            'role_id' => $role->id,
            'department_id' => $admissionsDept->id,
            'is_active' => true,
        ]);

        $this->assertTrue(Gate::forUser($admissionsUser)->allows('viewAny', AdmissionForm::class));
        $this->assertTrue(Gate::forUser($admissionsUser)->allows('viewAny', AdmissionFee::class));
        $this->assertTrue(Gate::forUser($admissionsUser)->allows('viewAny', Post::class));
        $this->assertFalse(Gate::forUser($admissionsUser)->allows('viewAny', AnnouncementPopup::class));
        $this->assertFalse(Gate::forUser($admissionsUser)->allows('viewAny', MandarinProgram::class));
        $this->assertFalse(Gate::forUser($admissionsUser)->allows('viewAny', HeroSlide::class));
        $this->assertFalse(Gate::forUser($admissionsUser)->allows('viewAny', SubjectResource::class));

        // 2. Academic
        $academicDept = Department::where('code', 'academic')->firstOrFail();
        $academicUser = User::factory()->create([
            'role_id' => $role->id,
            'department_id' => $academicDept->id,
            'is_active' => true,
        ]);

        $this->assertTrue(Gate::forUser($academicUser)->allows('viewAny', AnnouncementPopup::class));
        $this->assertTrue(Gate::forUser($academicUser)->allows('viewAny', SubjectResource::class));
        $this->assertTrue(Gate::forUser($academicUser)->allows('viewAny', Post::class));
        $this->assertFalse(Gate::forUser($academicUser)->allows('viewAny', AdmissionForm::class));
        $this->assertFalse(Gate::forUser($academicUser)->allows('viewAny', MandarinProgram::class));
        $this->assertFalse(Gate::forUser($academicUser)->allows('viewAny', HeroSlide::class));

        // 3. Chinese Learning Center (CLC)
        $clcDept = Department::where('code', 'clc')->firstOrFail();
        $clcUser = User::factory()->create([
            'role_id' => $role->id,
            'department_id' => $clcDept->id,
            'is_active' => true,
        ]);

        $this->assertTrue(Gate::forUser($clcUser)->allows('viewAny', MandarinProgram::class));
        $this->assertTrue(Gate::forUser($clcUser)->allows('viewAny', Post::class));
        $this->assertTrue(Gate::forUser($clcUser)->allows('viewAny', AnnouncementPopup::class));
        $this->assertFalse(Gate::forUser($clcUser)->allows('viewAny', AdmissionForm::class));
        $this->assertFalse(Gate::forUser($clcUser)->allows('viewAny', SubjectResource::class));

        // 4. Kindergarten
        $tkDept = Department::where('code', 'kindergarten')->firstOrFail();
        $tkUser = User::factory()->create([
            'role_id' => $role->id,
            'department_id' => $tkDept->id,
            'is_active' => true,
        ]);

        $this->assertTrue(Gate::forUser($tkUser)->allows('viewAny', HeroSlide::class));
        $this->assertTrue(Gate::forUser($tkUser)->allows('viewAny', Post::class));
        $this->assertTrue(Gate::forUser($tkUser)->allows('viewAny', AnnouncementPopup::class));
        $this->assertTrue(Gate::forUser($tkUser)->allows('viewAny', SubjectResource::class));
        $this->assertFalse(Gate::forUser($tkUser)->allows('viewAny', MandarinProgram::class));
        $this->assertFalse(Gate::forUser($tkUser)->allows('viewAny', AdmissionFee::class));

        // 5. Library
        $libDept = Department::where('code', 'library')->firstOrFail();
        $libUser = User::factory()->create([
            'role_id' => $role->id,
            'department_id' => $libDept->id,
            'is_active' => true,
        ]);

        $this->assertTrue(Gate::forUser($libUser)->allows('viewAny', SubjectResource::class));
        $this->assertFalse(Gate::forUser($libUser)->allows('viewAny', Post::class));
        $this->assertFalse(Gate::forUser($libUser)->allows('viewAny', AnnouncementPopup::class));
        $this->assertFalse(Gate::forUser($libUser)->allows('viewAny', AdmissionForm::class));
    }

    public function test_subject_resource_types_are_scoped_accurately(): void
    {
        $role = Role::where('code', 'department_admin')->firstOrFail();

        // Academic -> lesson_progress only
        $academicUser = User::factory()->create([
            'role_id' => $role->id,
            'department_id' => Department::where('code', 'academic')->value('id'),
            'is_active' => true,
        ]);
        $this->assertSame(['lesson_progress'], DepartmentPermission::getAllowedSubjectResourceTypes($academicUser));
        $this->assertTrue(DepartmentPermission::canAccessSubjectResourceType($academicUser, 'lesson_progress'));
        $this->assertFalse(DepartmentPermission::canAccessSubjectResourceType($academicUser, 'textbook'));

        // Student Affairs -> calendar, classroom_regulations
        $saUser = User::factory()->create([
            'role_id' => $role->id,
            'department_id' => Department::where('code', 'student_affairs')->value('id'),
            'is_active' => true,
        ]);
        $this->assertSame(['calendar', 'classroom_regulations'], DepartmentPermission::getAllowedSubjectResourceTypes($saUser));
        $this->assertTrue(DepartmentPermission::canAccessSubjectResourceType($saUser, 'calendar'));
        $this->assertTrue(DepartmentPermission::canAccessSubjectResourceType($saUser, 'classroom_regulations'));
        $this->assertFalse(DepartmentPermission::canAccessSubjectResourceType($saUser, 'syllabus'));

        // Library -> textbook only
        $libUser = User::factory()->create([
            'role_id' => $role->id,
            'department_id' => Department::where('code', 'library')->value('id'),
            'is_active' => true,
        ]);
        $this->assertSame(['textbook'], DepartmentPermission::getAllowedSubjectResourceTypes($libUser));
        $this->assertTrue(DepartmentPermission::canAccessSubjectResourceType($libUser, 'textbook'));
        $this->assertFalse(DepartmentPermission::canAccessSubjectResourceType($libUser, 'calendar'));

        // Super Admin -> null (all allowed)
        $superAdmin = User::factory()->create([
            'role_id' => Role::where('code', 'super_admin')->value('id'),
            'is_active' => true,
        ]);
        $this->assertNull(DepartmentPermission::getAllowedSubjectResourceTypes($superAdmin));
        $this->assertTrue(DepartmentPermission::canAccessSubjectResourceType($superAdmin, 'anything'));
    }

    public function test_filament_resources_can_view_any_respects_department(): void
    {
        $role = Role::where('code', 'department_admin')->firstOrFail();
        $admissionsUser = User::factory()->create([
            'role_id' => $role->id,
            'department_id' => Department::where('code', 'admissions')->value('id'),
            'is_active' => true,
        ]);

        $this->actingAs($admissionsUser);

        $this->assertTrue(AdmissionFormResource::canViewAny());
        $this->assertTrue(AdmissionFeeResource::canViewAny());
        $this->assertTrue(PostResource::canViewAny());
        $this->assertFalse(AnnouncementPopupResource::canViewAny());
        $this->assertFalse(SubjectResourceResource::canViewAny());
        $this->assertFalse(MandarinProgramResource::canViewAny());
        $this->assertFalse(HeroSlideResource::canViewAny());
    }

    public function test_direct_division_roles_authorize_models_without_manual_department_selection(): void
    {
        $admissionsRole = Role::where('code', 'admissions')->firstOrFail();
        $user = User::factory()->create([
            'role_id' => $admissionsRole->id,
            'is_active' => true,
        ]);

        $this->assertSame(Department::where('code', 'admissions')->value('id'), $user->department_id);
        $this->assertTrue(Gate::forUser($user)->allows('viewAny', AdmissionForm::class));
        $this->assertTrue(Gate::forUser($user)->allows('viewAny', AdmissionFee::class));
        $this->assertTrue(Gate::forUser($user)->allows('viewAny', Post::class));
        $this->assertFalse(Gate::forUser($user)->allows('viewAny', AnnouncementPopup::class));

        $this->actingAs($user);
        $this->assertTrue(AdmissionFormResource::canViewAny());
        $this->assertTrue(AdmissionFeeResource::canViewAny());
        $this->assertTrue(PostResource::canViewAny());
        $this->assertFalse(AnnouncementPopupResource::canViewAny());
        $this->assertFalse(SubjectResourceResource::canViewAny());
    }
}
