<?php

namespace App\Filament\Resources\SlideshowItems\Pages;

use App\Filament\Resources\SlideshowItems\SlideshowItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSlideshowItem extends EditRecord
{
    protected static string $resource = SlideshowItemResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
