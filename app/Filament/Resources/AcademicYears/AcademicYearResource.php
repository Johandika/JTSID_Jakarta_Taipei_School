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
                Section::make(__('admin.sections.academic_year'))
                    ->description(__('admin.descriptions.academic_year'))
                    ->schema([
                        TextInput::make('code')
                            ->label(__('admin.labels.academic_year_code'))
                            ->placeholder(__('admin.placeholders.academic_year_code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30),
                        TextInput::make('name')
                            ->label(__('admin.labels.academic_year_name'))
                            ->placeholder(__('admin.placeholders.academic_year_name'))
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
                    ->label(__('admin.fields.academic_year'))
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('code')
                    ->label(__('admin.fields.code'))
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
