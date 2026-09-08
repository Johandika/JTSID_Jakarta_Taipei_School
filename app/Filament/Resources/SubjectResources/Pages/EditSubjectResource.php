<?php

namespace App\Filament\Resources\SubjectResources\Pages;

use App\Filament\Resources\SubjectResources\SubjectResourceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubjectResource extends EditRecord
{
    protected static string $resource = SubjectResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
