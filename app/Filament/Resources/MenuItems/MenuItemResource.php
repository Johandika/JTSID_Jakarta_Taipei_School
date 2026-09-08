<?php

namespace App\Filament\Resources\MenuItems;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\MenuItem;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MenuItemResource extends CmsResource
{
    protected static ?string $model = MenuItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Navigation';

    protected static ?string $pluralModelLabel = 'Navigation';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('label')->required()->maxLength(150), CmsFields::safeUrl('url')->required(),
            Select::make('parent_id')->relationship('parent', 'label', fn (Builder $query, ?Model $record) => $query->whereNull('parent_id')->when($record, fn ($q) => $q->whereKeyNot($record->id)))->nullable(),
            Select::make('menu_area')->options(['header' => 'Header', 'footer' => 'Footer', 'utility' => 'Utility'])->required()->default('header'),
            TextInput::make('sort_order')->integer()->minValue(0)->default(0)->required(), Toggle::make('is_active')->default(true),
            CmsFields::translations(true)])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('label')->searchable(), TextColumn::make('url'), TextColumn::make('parent.label'), TextColumn::make('menu_area')->badge(), TextColumn::make('sort_order')->sortable(), IconColumn::make('is_active')->boolean()])

            ->defaultSort('id', 'desc')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
