<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\Page;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PageResource extends CmsResource
{
    protected static ?string $model = Page::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Pages';

    protected static ?string $pluralModelLabel = 'Pages';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('slug')->required()->maxLength(191)->unique(ignoreRecord: true)->rules(['regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/']),
            Select::make('template')->options(['default' => 'Standard page', 'academics' => 'Academics', 'admission' => 'Admissions'])->required()->default('default'),
            CmsFields::image('hero_image'), CmsFields::image('background_image'), CmsFields::safeUrl('gallery_url'),
            CmsFields::publishing(), CmsFields::translations()])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->label('Page'), TextColumn::make('slug')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('translation_status')->label('Translations')])
            ->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])])
            ->defaultSort('id', 'desc')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
