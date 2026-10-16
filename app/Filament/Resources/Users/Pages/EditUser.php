<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
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
        $data['role_id'] = Role::firstOrCreate(['code' => 'super_admin'], ['name' => 'Super Admin'])->id;
        $data['department_id'] = null;

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
