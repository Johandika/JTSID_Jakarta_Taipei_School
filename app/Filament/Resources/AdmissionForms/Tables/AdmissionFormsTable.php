<?php

namespace App\Filament\Resources\AdmissionForms\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdmissionFormsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school_level_label')
                    ->label('Jenjang')
                    ->badge()
                    ->sortable(query: fn ($query, string $direction) => $query->orderByRaw("case school_level when 'tk' then 1 when 'sd' then 2 when 'smp' then 3 when 'sma' then 4 else 5 end {$direction}")),
                TextColumn::make('title')
                    ->label('Judul')
                    ->weight('medium')
                    ->searchable(),
                TextColumn::make('file_path')
                    ->label('File')
                    ->limit(48),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->orderByRaw("case school_level when 'tk' then 1 when 'sd' then 2 when 'smp' then 3 when 'sma' then 4 else 5 end"));
    }
}
