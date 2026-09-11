<?php

namespace App\Filament\Resources\AnnouncementPopups\Pages;

use App\Filament\Resources\AnnouncementPopups\AnnouncementPopupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnnouncementPopups extends ListRecords
{
    protected static string $resource = AnnouncementPopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
