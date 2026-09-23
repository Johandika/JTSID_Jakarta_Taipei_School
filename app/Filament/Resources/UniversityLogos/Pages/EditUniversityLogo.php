<?php

namespace App\Filament\Resources\UniversityLogos\Pages;

use App\Filament\Resources\UniversityLogos\UniversityLogoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUniversityLogo extends EditRecord
{
    protected static string $resource = UniversityLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
