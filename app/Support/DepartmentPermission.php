<?php

namespace App\Support;

use App\Models\AdmissionFee;
use App\Models\AdmissionForm;
use App\Models\AnnouncementPopup;
use App\Models\ContactMessage;
use App\Models\HeroSlide;
use App\Models\MandarinProgram;
use App\Models\Post;
use App\Models\SubjectResource;
use App\Models\User;

class DepartmentPermission
{
    /**
     * Permission matrix defining allowed models and subject resource types for each department.
     *
     * @var array<string, array{models: array<class-string>, subject_resource_types: array<string>}>
     */
    public const MATRIX = [
        'admissions' => [
            'models' => [
                AdmissionForm::class,
                AdmissionFee::class,
                ContactMessage::class,
                Post::class,
            ],
            'subject_resource_types' => [],
        ],
        'academic' => [
            'models' => [
                AnnouncementPopup::class,
                SubjectResource::class,
                Post::class,
            ],
            'subject_resource_types' => ['lesson_progress'],
        ],
        'student_affairs' => [
            'models' => [
                SubjectResource::class,
                Post::class,
                AnnouncementPopup::class,
            ],
            'subject_resource_types' => ['calendar', 'classroom_regulations'],
        ],
        'clc' => [
            'models' => [
                MandarinProgram::class,
                Post::class,
                AnnouncementPopup::class,
            ],
            'subject_resource_types' => [],
        ],
        'kindergarten' => [
            'models' => [
                HeroSlide::class,
                Post::class,
                AnnouncementPopup::class,
                SubjectResource::class,
            ],
            'subject_resource_types' => ['textbook', 'calendar', 'lesson_progress', 'syllabus', 'weekly_news'],
        ],
        'secretary' => [
            'models' => [
                SubjectResource::class,
                Post::class,
                AnnouncementPopup::class,
            ],
            'subject_resource_types' => ['weekly_news'],
        ],
        'languages' => [
            'models' => [
                Post::class,
                SubjectResource::class,
                AnnouncementPopup::class,
            ],
            'subject_resource_types' => ['weekly_news'],
        ],
        'library' => [
            'models' => [
                SubjectResource::class,
            ],
            'subject_resource_types' => ['textbook'],
        ],
    ];

    /**
     * Check if a user can access a specific model class based on role & department.
     */
    /**
     * Check if a role code represents a department role (or legacy department_admin).
     */
    public static function isDepartmentRole(?string $roleCode): bool
    {
        return $roleCode !== null && (array_key_exists($roleCode, self::MATRIX) || $roleCode === 'department_admin');
    }

    /**
     * Get the department code for a user based on their role code or assigned department.
     */
    public static function getDepartmentCode(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        $roleCode = $user->role?->code;

        if ($roleCode !== null && array_key_exists($roleCode, self::MATRIX)) {
            return $roleCode;
        }

        return $user->department?->code;
    }

    /**
     * Check if a user can access a specific model class based on role & department.
     */
    public static function canAccessModel(?User $user, string $modelClass): bool
    {
        if (! $user || ! $user->is_active || ! $user->role) {
            return false;
        }

        $roleCode = $user->role->code;

        if (in_array($roleCode, ['super_admin', 'content_admin', 'viewer'], true)) {
            return true;
        }

        if (! self::isDepartmentRole($roleCode)) {
            return false;
        }

        $deptCode = self::getDepartmentCode($user);
        if (! $deptCode) {
            return false;
        }

        $allowedModels = self::MATRIX[$deptCode]['models'] ?? [];

        return in_array($modelClass, $allowedModels, true);
    }

    /**
     * Get allowed subject resource type codes for a user.
     * Returns null if the user has unrestricted access (e.g. super_admin or content_admin).
     *
     * @return array<string>|null
     */
    public static function getAllowedSubjectResourceTypes(?User $user): ?array
    {
        if (! $user || ! $user->is_active || ! $user->role) {
            return [];
        }

        if (in_array($user->role->code, ['super_admin', 'content_admin'], true)) {
            return null;
        }

        if (! self::isDepartmentRole($user->role->code)) {
            return [];
        }

        $deptCode = self::getDepartmentCode($user);
        if (! $deptCode) {
            return [];
        }

        return self::MATRIX[$deptCode]['subject_resource_types'] ?? [];
    }

    /**
     * Check if a user can access a specific subject resource type code.
     */
    public static function canAccessSubjectResourceType(?User $user, ?string $typeCode): bool
    {
        $allowed = self::getAllowedSubjectResourceTypes($user);

        if ($allowed === null) {
            return true;
        }

        if (empty($typeCode)) {
            return false;
        }

        return in_array($typeCode, $allowed, true);
    }
}
