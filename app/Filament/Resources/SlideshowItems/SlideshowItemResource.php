<?php

namespace App\Filament\Resources\SlideshowItems;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\SlideshowItem;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SlideshowItemResource extends CmsResource
{
    protected static ?string $model = SlideshowItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Highlights';

    protected static ?string $pluralModelLabel = 'Highlights';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('topic')->required()->unique(ignoreRecord: true)->maxLength(191), CmsFields::image('image', 1024)->required(),
            CmsFields::safeUrl('link_url'), TextInput::make('sort_order')->integer()->minValue(0)->default(0)->required(),
            Toggle::make('is_active')->default(true)])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([ImageColumn::make('image')->disk('public'), TextColumn::make('topic')->searchable(), TextColumn::make('sort_order')->sortable(), IconColumn::make('is_active')->boolean()])

            ->defaultSort('id', 'desc')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlideshowItems::route('/'),
            'create' => Pages\CreateSlideshowItem::route('/create'),
            'edit' => Pages\EditSlideshowItem::route('/{record}/edit'),
        ];
    }
}
