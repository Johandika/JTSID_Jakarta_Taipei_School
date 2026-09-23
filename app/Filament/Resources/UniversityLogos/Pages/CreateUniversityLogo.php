<?php

namespace App\Filament\Resources\UniversityLogos\Pages;

use App\Filament\Resources\UniversityLogos\UniversityLogoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUniversityLogo extends CreateRecord
{
    protected static string $resource = UniversityLogoResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
