<?php

namespace App\Filament\Resources\SubjectResources;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\SubjectResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubjectResourceResource extends CmsResource
{
    protected static ?string $model = SubjectResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Subject Resources';

    protected static ?string $pluralModelLabel = 'Subject Resources';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('topic')->required()->maxLength(191), CmsFields::department(),
            Select::make('type_id')->relationship('type', 'name')->required(),
            Select::make('grade_level_id')->relationship('gradeLevel', 'name')->required(),
            CmsFields::uploadDate(), CmsFields::document(true), CmsFields::publishing(), CmsFields::translations()])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('topic')->searchable(), TextColumn::make('type.name'), TextColumn::make('gradeLevel.name'), TextColumn::make('department.name'), TextColumn::make('status')->badge(), TextColumn::make('translation_status')->label('Translations')])
            ->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])])
            ->defaultSort('upload_date', 'desc')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjectResources::route('/'),
            'create' => Pages\CreateSubjectResource::route('/create'),
            'edit' => Pages\EditSubjectResource::route('/{record}/edit'),
        ];
    }
}
