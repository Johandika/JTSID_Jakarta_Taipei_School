<?php

namespace App\Policies;

use App\Models\AdmissionForm;
use App\Models\User;
use App\Support\DepartmentPermission;

class AdmissionFormPolicy
{
    public function viewAny(User $user): bool
    {
        return DepartmentPermission::canAccessModel($user, AdmissionForm::class);
    }

    public function view(User $user, AdmissionForm $record): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user) && in_array($user->role?->code, ['super_admin', 'content_admin', 'department_admin'], true);
    }

    public function update(User $user, AdmissionForm $record): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, AdmissionForm $record): bool
    {
        return $this->create($user);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
