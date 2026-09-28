<?php

namespace App\Filament\Resources\AdmissionForms\Pages;

use App\Filament\Resources\AdmissionForms\AdmissionFormResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdmissionForm extends EditRecord
{
    protected static string $resource = AdmissionFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
