<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Department;
use App\Models\Role;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $role = Role::find($data['role_id'] ?? null);
        if ($role && $role->code !== 'super_admin' && $role->code !== 'content_admin' && $role->code !== 'viewer') {
            $data['department_id'] = Department::where('code', $role->code)->value('id');
        } else {
            $data['department_id'] = null;
        }

        if ($this->getRecord()->id === Filament::auth()->id()) {
            $data['is_active'] = true;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->getRecord()->id === Filament::auth()->id()) {
            Filament::auth()->setUser($this->getRecord());
            session()->put('password_hash_'.Filament::getAuthGuard(), $this->getRecord()->getAuthPassword());
        }
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
