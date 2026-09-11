<?php

namespace App\Filament\Resources\AnnouncementPopups\Pages;

use App\Filament\Resources\AnnouncementPopups\AnnouncementPopupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncementPopup extends EditRecord
{
    protected static string $resource = AnnouncementPopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
