<?php

namespace App\Filament\Resources\UniversityAcceptances;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\UniversityAcceptance;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UniversityAcceptanceResource extends CmsResource
{
    protected static ?string $model = UniversityAcceptance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $navigationLabel = 'University Acceptances';

    protected static ?string $pluralModelLabel = 'University Acceptances';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('student_name')->required()->maxLength(150), TextInput::make('university_name')->required()->maxLength(191),
            Select::make('academic_year_id')->relationship('academicYear', 'name')->required(), CmsFields::department(),
            TextInput::make('program_name')->maxLength(191), TextInput::make('admission_path')->maxLength(150), TextInput::make('country')->maxLength(100),
            TextInput::make('sort_order')->integer()->minValue(0)->default(0)->required(), Toggle::make('is_active')->default(true)])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('student_name')->searchable(), TextColumn::make('university_name')->searchable(), TextColumn::make('academicYear.name'), TextColumn::make('program_name'), TextColumn::make('country')])

            ->defaultSort('id', 'desc')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUniversityAcceptances::route('/'),
            'create' => Pages\CreateUniversityAcceptance::route('/create'),
            'edit' => Pages\EditUniversityAcceptance::route('/{record}/edit'),
        ];
    }
}
