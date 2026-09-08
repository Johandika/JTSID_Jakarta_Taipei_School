<?php

namespace App\Filament\Resources\SubjectResources\Pages;

use App\Filament\Resources\SubjectResources\SubjectResourceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubjectResources extends ListRecords
{
    protected static string $resource = SubjectResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
