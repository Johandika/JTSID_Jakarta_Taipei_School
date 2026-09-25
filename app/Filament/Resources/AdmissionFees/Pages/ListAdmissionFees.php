<?php

namespace App\Filament\Resources\AdmissionFees\Pages;

use App\Filament\Resources\AdmissionFees\AdmissionFeeResource;
use App\Models\AdmissionFee;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAdmissionFees extends ListRecords
{
    protected static string $resource = AdmissionFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(fn () => match ($this->activeTab) {
                    'sd' => 'Tambah Biaya SD',
                    'smp' => 'Tambah Biaya SMP',
                    'sma' => 'Tambah Biaya SMA',
                    default => 'Tambah Biaya TK',
                })
                ->url(fn (): string => static::getResource()::getUrl('create', [
                    'grade_level' => in_array($this->activeTab, ['tk', 'sd', 'smp', 'sma'], true) ? $this->activeTab : 'tk',
                ])),
        ];
    }

    public function getTabs(): array
    {
        return [
            'tk' => Tab::make('Taman Kanak-Kanak (TK)')
                ->icon('heroicon-o-sparkles')
                ->badge(AdmissionFee::whereHas('gradeLevel', fn ($q) => $q->where('code', 'tk'))->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('gradeLevel', fn ($q) => $q->where('code', 'tk'))),

            'sd' => Tab::make('Sekolah Dasar (SD)')
                ->icon('heroicon-o-book-open')
                ->badge(AdmissionFee::whereHas('gradeLevel', fn ($q) => $q->where('code', 'sd'))->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('gradeLevel', fn ($q) => $q->where('code', 'sd'))),

            'smp' => Tab::make('Sekolah Menengah Pertama (SMP)')
                ->icon('heroicon-o-academic-cap')
                ->badge(AdmissionFee::whereHas('gradeLevel', fn ($q) => $q->where('code', 'smp'))->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('gradeLevel', fn ($q) => $q->where('code', 'smp'))),

            'sma' => Tab::make('Sekolah Menengah Atas (SMA)')
                ->icon('heroicon-o-check-badge')
                ->badge(AdmissionFee::whereHas('gradeLevel', fn ($q) => $q->where('code', 'sma'))->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('gradeLevel', fn ($q) => $q->where('code', 'sma'))),
        ];
    }
}
