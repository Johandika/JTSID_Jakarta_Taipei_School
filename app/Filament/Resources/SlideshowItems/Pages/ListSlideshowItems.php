<?php

namespace App\Filament\Resources\SlideshowItems\Pages;

use App\Filament\Resources\SlideshowItems\SlideshowItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSlideshowItems extends ListRecords
{
    protected static string $resource = SlideshowItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
