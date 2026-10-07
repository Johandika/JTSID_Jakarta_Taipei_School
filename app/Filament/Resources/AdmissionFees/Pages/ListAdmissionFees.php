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
                    'sd' => __('admin.actions.add_sd_fee'),
                    'smp' => __('admin.actions.add_smp_fee'),
                    'sma' => __('admin.actions.add_sma_fee'),
                    default => __('admin.actions.add_tk_fee'),
                })
                ->url(fn (): string => static::getResource()::getUrl('create', [
                    'grade_level' => in_array($this->activeTab, ['tk', 'sd', 'smp', 'sma'], true) ? $this->activeTab : 'tk',
                ])),
        ];
    }

    public function getTabs(): array
    {
        return [
            'tk' => Tab::make(__('admin.school_levels.tk'))
                ->icon('heroicon-o-sparkles')
                ->badge(AdmissionFee::whereHas('gradeLevel', fn ($q) => $q->where('code', 'tk'))->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('gradeLevel', fn ($q) => $q->where('code', 'tk'))),

            'sd' => Tab::make(__('admin.school_levels.sd'))
                ->icon('heroicon-o-book-open')
                ->badge(AdmissionFee::whereHas('gradeLevel', fn ($q) => $q->where('code', 'sd'))->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('gradeLevel', fn ($q) => $q->where('code', 'sd'))),

            'smp' => Tab::make(__('admin.school_levels.smp'))
                ->icon('heroicon-o-academic-cap')
                ->badge(AdmissionFee::whereHas('gradeLevel', fn ($q) => $q->where('code', 'smp'))->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('gradeLevel', fn ($q) => $q->where('code', 'smp'))),

            'sma' => Tab::make(__('admin.school_levels.sma'))
                ->icon('heroicon-o-check-badge')
                ->badge(AdmissionFee::whereHas('gradeLevel', fn ($q) => $q->where('code', 'sma'))->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('gradeLevel', fn ($q) => $q->where('code', 'sma'))),
        ];
    }
}
