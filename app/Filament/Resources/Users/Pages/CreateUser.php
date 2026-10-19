<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Department;
use App\Models\Role;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $role = Role::find($data['role_id'] ?? null);
        if ($role && $role->code !== 'super_admin' && $role->code !== 'content_admin' && $role->code !== 'viewer') {
            $data['department_id'] = Department::where('code', $role->code)->value('id');
        } else {
            $data['department_id'] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
