<?php

namespace App\Policies;

use App\Models\AdmissionFee;
use App\Models\User;
use App\Support\DepartmentPermission;

class AdmissionFeePolicy
{
    public function viewAny(User $user): bool
    {
        return DepartmentPermission::canAccessModel($user, AdmissionFee::class);
    }

    public function view(User $user, AdmissionFee $record): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user) && (
            in_array($user->role?->code, ['super_admin', 'content_admin'], true)
            || DepartmentPermission::isDepartmentRole($user->role?->code)
        );
    }

    public function update(User $user, AdmissionFee $record): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, AdmissionFee $record): bool
    {
        return $this->create($user);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
