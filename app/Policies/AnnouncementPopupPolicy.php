<?php

namespace App\Policies;

use App\Models\AnnouncementPopup;
use App\Models\User;
use App\Support\DepartmentPermission;

class AnnouncementPopupPolicy
{
    public function viewAny(User $user): bool
    {
        return DepartmentPermission::canAccessModel($user, AnnouncementPopup::class);
    }

    public function view(User $user, AnnouncementPopup $record): bool
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

    public function update(User $user, AnnouncementPopup $record): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, AnnouncementPopup $record): bool
    {
        return $this->create($user);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
