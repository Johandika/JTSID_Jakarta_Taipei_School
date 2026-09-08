<?php

namespace App\Filament\Resources\StaffProfiles\Pages;

use App\Filament\Resources\StaffProfiles\StaffProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaffProfile extends CreateRecord
{
    protected static string $resource = StaffProfileResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
