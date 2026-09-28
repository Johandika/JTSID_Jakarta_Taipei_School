<?php

namespace App\Filament\Resources\AdmissionForms\Pages;

use App\Filament\Resources\AdmissionForms\AdmissionFormResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdmissionForm extends CreateRecord
{
    protected static string $resource = AdmissionFormResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
