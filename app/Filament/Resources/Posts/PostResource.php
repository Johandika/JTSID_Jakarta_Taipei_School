<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\Post;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
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
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make(__('admin.sections.main_information'))
                        ->description(__('admin.descriptions.main_information'))
                        ->schema([
                            TextInput::make('topic')
                                ->label(__('admin.labels.topic_news'))
                                ->required()
                                ->maxLength(191),
                            TextInput::make('slug')
                                ->label(__('admin.fields.slug_url'))
                                ->helperText(__('admin.helpers.slug_format'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(191)
                                ->rules(['regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/']),
                        ])
                        ->columns(2),

                    Section::make(__('admin.sections.content_translations'))
                        ->description(__('admin.descriptions.content_translations'))
                        ->schema([
                            CmsFields::translations(__('admin.labels.news_title')),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    CmsFields::publishing(),

                    Section::make(__('admin.sections.classification_targets'))
                        ->description(__('admin.descriptions.classification_targets'))
                        ->schema([
                            Select::make('category_id')
                                ->label(__('admin.labels.news_category'))
                                ->relationship('category', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            CmsFields::department(),
                            CmsFields::schoolLevels(),
                        ]),

                    Section::make(__('admin.sections.media_attachments'))
                        ->description(__('admin.descriptions.media_attachments'))
                        ->schema([
                            CmsFields::uploadDate(),
                            CmsFields::image('image', 4096, __('admin.labels.news_cover')),
                            CmsFields::document(false, __('admin.labels.document_optional')),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('topic')
                    ->label(__('admin.fields.news_topic'))
                    ->searchable()
                    ->weight('medium')
                    ->limit(40),
                TextColumn::make('category.name')
                    ->label(__('admin.fields.category'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('school_level_label')
                    ->label(__('admin.fields.school_level')),
                TextColumn::make('department.name')
                    ->label(__('admin.fields.department')),
                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge(),
                TextColumn::make('upload_date')
                    ->label(__('admin.fields.upload_date'))
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('translation_status')
                    ->label(__('admin.fields.translations'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => __('admin.options.draft'),
                        'published' => __('admin.options.published'),
                        'archived' => __('admin.options.archived'),
                    ]),
            ])
            ->defaultSort('upload_date', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
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
