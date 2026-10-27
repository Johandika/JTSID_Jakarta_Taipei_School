<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;
use App\Support\DepartmentPermission;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return DepartmentPermission::canAccessModel($user, ContactMessage::class);
    }

    public function view(User $user, ContactMessage $record): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Messages only arrive through the public contact form.
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ContactMessage $record): bool
    {
        return $this->viewAny($user) && (
            in_array($user->role?->code, ['super_admin', 'content_admin'], true)
            || DepartmentPermission::isDepartmentRole($user->role?->code)
        );
    }

    public function delete(User $user, ContactMessage $record): bool
    {
        return $this->update($user, $record);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
