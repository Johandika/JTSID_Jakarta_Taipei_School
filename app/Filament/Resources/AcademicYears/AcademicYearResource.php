<?php

namespace App\Filament\Resources\AcademicYears;

use App\Filament\Resources\CmsResource;
use App\Models\AcademicYear;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AcademicYearResource extends CmsResource
{
    protected static ?string $model = AcademicYear::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Academic Years';

    protected static ?string $pluralModelLabel = 'Academic Years';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Tahun Ajaran (Academic Year)')
                    ->description('Identifikasi tahun ajaran sekolah')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Tahun Ajaran')
                            ->placeholder('Contoh: 2025/2026')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30),
                        TextInput::make('name')
                            ->label('Nama Tahun Ajaran')
                            ->placeholder('Contoh: Tahun Ajaran 2025/2026')
                            ->required()
                            ->maxLength(150),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYear::route('/create'),
            'edit' => Pages\EditAcademicYear::route('/{record}/edit'),
        ];
    }
}
