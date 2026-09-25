<?php

namespace App\Filament\Resources\MandarinPrograms\Pages;

use App\Filament\Resources\MandarinPrograms\MandarinProgramResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMandarinProgram extends CreateRecord
{
    protected static string $resource = MandarinProgramResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
