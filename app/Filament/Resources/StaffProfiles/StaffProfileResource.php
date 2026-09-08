<?php

namespace App\Filament\Resources\StaffProfiles;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\StaffProfile;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StaffProfileResource extends CmsResource
{
    protected static ?string $model = StaffProfile::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $navigationLabel = 'Staff';

    protected static ?string $pluralModelLabel = 'Staff';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(150), TextInput::make('title')->required()->maxLength(150),
            CmsFields::department(), TextInput::make('extension')->maxLength(50), Textarea::make('responsibilities'),
            CmsFields::image('photo'), TextInput::make('sort_order')->integer()->minValue(0)->default(0)->required(), Toggle::make('is_active')->default(true)])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('title'), TextColumn::make('department.name'), TextColumn::make('extension'), IconColumn::make('is_active')->boolean()])

            ->defaultSort('id', 'desc')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffProfiles::route('/'),
            'create' => Pages\CreateStaffProfile::route('/create'),
            'edit' => Pages\EditStaffProfile::route('/{record}/edit'),
        ];
    }
}
