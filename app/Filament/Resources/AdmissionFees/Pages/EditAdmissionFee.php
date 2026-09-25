<?php

namespace App\Filament\Resources\AdmissionFees\Pages;

use App\Filament\Resources\AdmissionFees\AdmissionFeeResource;
use App\Models\AdmissionFee;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdmissionFee extends EditRecord
{
    protected static string $resource = AdmissionFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        /** @var AdmissionFee $record */
        $record = $this->record;
        $gradeCode = $record?->gradeLevel?->code;

        return static::getResource()::getUrl('index', array_filter([
            'tab' => $gradeCode,
        ]));
    }
}
