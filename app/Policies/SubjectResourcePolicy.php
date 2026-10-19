<?php

namespace App\Policies;

use App\Models\SubjectResource;
use App\Models\User;
use App\Support\DepartmentPermission;

class SubjectResourcePolicy
{
    public function viewAny(User $user): bool
    {
        $allowedTypes = DepartmentPermission::getAllowedSubjectResourceTypes($user);

        return DepartmentPermission::canAccessModel($user, SubjectResource::class)
            && ($allowedTypes === null || count($allowedTypes) > 0);
    }

    public function view(User $user, SubjectResource $record): bool
    {
        return $this->owns($user, $record);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user) && in_array($user->role?->code, ['super_admin', 'content_admin', 'department_admin'], true);
    }

    public function update(User $user, SubjectResource $record): bool
    {
        return $this->create($user) && $this->owns($user, $record);
    }

    public function delete(User $user, SubjectResource $record): bool
    {
        return $this->update($user, $record);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    private function owns(User $user, SubjectResource $record): bool
    {
        if (! $this->viewAny($user)) {
            return false;
        }

        if ($user->role?->code !== 'department_admin') {
            return true;
        }

        if ($user->department_id === null || (int) $record->department_id !== (int) $user->department_id) {
            return false;
        }

        $typeCode = $record->relationLoaded('type') ? $record->type?->code : $record->type()->value('code');

        return DepartmentPermission::canAccessSubjectResourceType($user, $typeCode);
    }
}
