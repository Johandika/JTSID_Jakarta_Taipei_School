<?php

namespace App\Filament\Resources\SlideshowItems\Pages;

use App\Filament\Resources\SlideshowItems\SlideshowItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSlideshowItem extends CreateRecord
{
    protected static string $resource = SlideshowItemResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
