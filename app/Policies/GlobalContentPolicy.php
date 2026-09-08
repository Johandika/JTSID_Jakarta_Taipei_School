<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class GlobalContentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && in_array($user->role?->code, ['super_admin', 'content_admin', 'viewer']);
    }

    public function view(User $user, Model $record): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->is_active && in_array($user->role?->code, ['super_admin', 'content_admin']);
    }

    public function update(User $user, Model $record): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->create($user);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
