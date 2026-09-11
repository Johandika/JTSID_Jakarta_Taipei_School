<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\Post;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostResource extends CmsResource
{
    protected static ?string $model = Post::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'News & Media';

    protected static ?string $pluralModelLabel = 'News & Media';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('topic')->required()->maxLength(191),
            TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(191)->rules(['regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/']),
            CmsFields::department(), Select::make('category_id')->relationship('category', 'name')->required(),
            CmsFields::schoolLevels(), CmsFields::uploadDate(), CmsFields::image(), CmsFields::document(), CmsFields::publishing(), CmsFields::translations()])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('topic')->searchable(), TextColumn::make('school_level_label')->label('School levels'), TextColumn::make('department.name'), TextColumn::make('category.name'), TextColumn::make('status')->badge(), TextColumn::make('upload_date')->date()->sortable(), TextColumn::make('translation_status')->label('Translations')])
            ->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])])
            ->defaultSort('upload_date', 'desc')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
