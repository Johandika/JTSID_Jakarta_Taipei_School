<?php

namespace App\Filament\Resources\UniversityAcceptances\Pages;

use App\Filament\Resources\UniversityAcceptances\UniversityAcceptanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUniversityAcceptance extends EditRecord
{
    protected static string $resource = UniversityAcceptanceResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
