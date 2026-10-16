<?php

namespace App\Filament\Resources\AcademicPrograms\Pages;

use App\Filament\Resources\AcademicPrograms\AcademicProgramResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicProgram extends CreateRecord
{
    protected static string $resource = AcademicProgramResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
