<?php

namespace App\Filament\Resources\AdmissionFees\Pages;

use App\Filament\Resources\AdmissionFees\AdmissionFeeResource;
use App\Models\AdmissionFee;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\Url;

class CreateAdmissionFee extends CreateRecord
{
    protected static string $resource = AdmissionFeeResource::class;

    #[Url]
    public ?string $grade_level = null;

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
