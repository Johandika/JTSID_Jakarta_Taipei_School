<?php

namespace App\Filament\Resources\SubjectResources\Pages;

use App\Filament\Resources\SubjectResources\SubjectResourceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubjectResource extends CreateRecord
{
    protected static string $resource = SubjectResourceResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
