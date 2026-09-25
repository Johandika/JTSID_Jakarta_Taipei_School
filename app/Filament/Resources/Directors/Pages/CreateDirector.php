<?php

namespace App\Filament\Resources\Directors\Pages;

use App\Filament\Resources\Directors\DirectorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDirector extends CreateRecord
{
    protected static string $resource = DirectorResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
