<?php

namespace App\Filament\Resources\UniversityLogos\Pages;

use App\Filament\Resources\UniversityLogos\UniversityLogoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUniversityLogos extends ListRecords
{
    protected static string $resource = UniversityLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
