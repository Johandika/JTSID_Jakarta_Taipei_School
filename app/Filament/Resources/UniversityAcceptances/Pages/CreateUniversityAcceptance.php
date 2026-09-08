<?php

namespace App\Filament\Resources\UniversityAcceptances\Pages;

use App\Filament\Resources\UniversityAcceptances\UniversityAcceptanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUniversityAcceptance extends CreateRecord
{
    protected static string $resource = UniversityAcceptanceResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
