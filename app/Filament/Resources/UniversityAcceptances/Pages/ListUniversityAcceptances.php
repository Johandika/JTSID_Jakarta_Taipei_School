<?php

namespace App\Filament\Resources\UniversityAcceptances\Pages;

use App\Filament\Resources\UniversityAcceptances\UniversityAcceptanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUniversityAcceptances extends ListRecords
{
    protected static string $resource = UniversityAcceptanceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
