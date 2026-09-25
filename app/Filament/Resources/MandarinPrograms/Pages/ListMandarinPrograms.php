<?php

namespace App\Filament\Resources\MandarinPrograms\Pages;

use App\Filament\Resources\MandarinPrograms\MandarinProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMandarinPrograms extends ListRecords
{
    protected static string $resource = MandarinProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
