<?php

namespace App\Filament\Resources\Directors\Pages;

use App\Filament\Resources\Directors\DirectorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDirector extends EditRecord
{
    protected static string $resource = DirectorResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
