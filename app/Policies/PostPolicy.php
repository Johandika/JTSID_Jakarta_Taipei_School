<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Support\DepartmentPermission;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return DepartmentPermission::canAccessModel($user, Post::class);
    }

    public function view(User $user, Post $post): bool
    {
        return $this->owns($user, $post);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user) && in_array($user->role?->code, ['super_admin', 'content_admin', 'department_admin'], true);
    }

    public function update(User $user, Post $post): bool
    {
        return $this->create($user) && $this->owns($user, $post);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    private function owns(User $user, Post $post): bool
    {
        return $this->viewAny($user) && ($user->role?->code !== 'department_admin'
            || ($user->department_id !== null && (int) $post->department_id === (int) $user->department_id));
    }
}
