<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ContentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->role !== null;
    }

    public function view(User $user, Model $record): bool
    {
        return $this->owns($user, $record);
    }

    public function create(User $user): bool
    {
        return $user->is_active && in_array($user->role?->code, ['super_admin', 'content_admin', 'department_admin']);
    }

    public function update(User $user, Model $record): bool
    {
        return $this->create($user) && $this->owns($user, $record);
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->update($user, $record);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    private function owns(User $user, Model $record): bool
    {
        return $this->viewAny($user) && ($user->role?->code !== 'department_admin'
            || ($user->department_id !== null && (int) $record->department_id === (int) $user->department_id));
    }
}
