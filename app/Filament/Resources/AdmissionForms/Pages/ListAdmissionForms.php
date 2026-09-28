<?php

namespace App\Filament\Resources\AdmissionForms\Pages;

use App\Filament\Resources\AdmissionForms\AdmissionFormResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmissionForms extends ListRecords
{
    protected static string $resource = AdmissionFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => AdmissionFormResource::canCreate()),
        ];
    }
}
