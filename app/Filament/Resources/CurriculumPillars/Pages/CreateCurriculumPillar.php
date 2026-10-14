<?php

namespace App\Filament\Resources\CurriculumPillars\Pages;

use App\Filament\Resources\CurriculumPillars\CurriculumPillarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCurriculumPillar extends CreateRecord
{
    protected static string $resource = CurriculumPillarResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
