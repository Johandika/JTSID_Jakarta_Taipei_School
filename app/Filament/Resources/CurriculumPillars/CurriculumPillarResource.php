<?php

namespace App\Filament\Resources\CurriculumPillars;

use App\Filament\Resources\CmsResource;
use App\Filament\Resources\CurriculumPillars\Pages\CreateCurriculumPillar;
use App\Filament\Resources\CurriculumPillars\Pages\EditCurriculumPillar;
use App\Filament\Resources\CurriculumPillars\Pages\ListCurriculumPillars;
use App\Filament\Support\CmsFields;
use App\Models\CurriculumPillar;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurriculumPillarResource extends CmsResource
{
    protected static ?string $model = CurriculumPillar::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make(__('admin.curriculum_pillars.sections.content'))
                            ->description(__('admin.curriculum_pillars.sections.content_desc'))
                            ->schema([
                                Tabs::make('CurriculumTranslations')
                                    ->tabs([
                                        Tab::make('en')
                                            ->label(__('admin.curriculum_pillars.tabs.en'))
                                            ->schema([
                                                TextInput::make('title.en')
                                                    ->label(__('admin.curriculum_pillars.fields.title_en'))
                                                    ->placeholder(__('admin.curriculum_pillars.fields.title_placeholder_en'))
                                                    ->required()
                                                    ->maxLength(150),
                                                Textarea::make('description.en')
                                                    ->label(__('admin.curriculum_pillars.fields.description_en'))
                                                    ->placeholder(__('admin.curriculum_pillars.fields.description_placeholder_en'))
                                                    ->rows(4)
                                                    ->required()
                                                    ->maxLength(500),
                                                TagsInput::make('tags.en')
                                                    ->label(__('admin.curriculum_pillars.fields.tags_en'))
                                                    ->placeholder(__('admin.curriculum_pillars.fields.tags_placeholder'))
                                                    ->helperText(__('admin.curriculum_pillars.fields.tags_helper_en')),
                                            ]),

                                        Tab::make('id')
                                            ->label(__('admin.curriculum_pillars.tabs.id'))
                                            ->schema([
                                                TextInput::make('title.id')
                                                    ->label(__('admin.curriculum_pillars.fields.title_id'))
                                                    ->placeholder(__('admin.curriculum_pillars.fields.title_placeholder_id'))
                                                    ->required()
                                                    ->maxLength(150),
                                                Textarea::make('description.id')
                                                    ->label(__('admin.curriculum_pillars.fields.description_id'))
                                                    ->placeholder(__('admin.curriculum_pillars.fields.description_placeholder_id'))
                                                    ->rows(4)
                                                    ->required()
                                                    ->maxLength(500),
                                                TagsInput::make('tags.id')
                                                    ->label(__('admin.curriculum_pillars.fields.tags_id'))
                                                    ->placeholder(__('admin.curriculum_pillars.fields.tags_placeholder'))
                                                    ->helperText(__('admin.curriculum_pillars.fields.tags_helper_id')),
                                            ]),

                                        Tab::make('zh-TW')
                                            ->label(__('admin.curriculum_pillars.tabs.zh'))
                                            ->schema([
                                                TextInput::make('title.zh-TW')
                                                    ->label(__('admin.curriculum_pillars.fields.title_zh'))
                                                    ->placeholder(__('admin.curriculum_pillars.fields.title_placeholder_zh'))
                                                    ->required()
                                                    ->maxLength(150),
                                                Textarea::make('description.zh-TW')
                                                    ->label(__('admin.curriculum_pillars.fields.description_zh'))
                                                    ->placeholder(__('admin.curriculum_pillars.fields.description_placeholder_zh'))
                                                    ->rows(4)
                                                    ->required()
                                                    ->maxLength(500),
                                                TagsInput::make('tags.zh-TW')
                                                    ->label(__('admin.curriculum_pillars.fields.tags_zh'))
                                                    ->placeholder(__('admin.curriculum_pillars.fields.tags_placeholder'))
                                                    ->helperText(__('admin.curriculum_pillars.fields.tags_helper_zh')),
                                            ]),
                                    ]),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 1])
                    ->schema([
                        Section::make(__('admin.curriculum_pillars.sections.visual_settings'))
                            ->description(__('admin.curriculum_pillars.sections.visual_settings_desc'))
                            ->schema([
                                CmsFields::image('icon', 2048, __('admin.curriculum_pillars.fields.icon'))
                                    ->label(__('admin.curriculum_pillars.fields.icon'))
                                    ->helperText(__('admin.curriculum_pillars.fields.icon_helper')),

                                Select::make('color_theme')
                                    ->label(__('admin.curriculum_pillars.fields.color_theme'))
                                    ->options(CurriculumPillar::getColorThemeOptions())
                                    ->default('blue')
                                    ->required()
                                    ->native(false)
                                    ->helperText(__('admin.curriculum_pillars.fields.color_theme_helper')),

                                TextInput::make('sort_order')
                                    ->label(__('admin.curriculum_pillars.fields.sort_order'))
                                    ->numeric()
                                    ->default(0)
                                    ->helperText(__('admin.curriculum_pillars.fields.sort_order_helper')),

                                Toggle::make('is_active')
                                    ->label(__('admin.curriculum_pillars.fields.is_active'))
                                    ->default(true)
                                    ->helperText(__('admin.curriculum_pillars.fields.is_active_helper')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon')
                    ->label(__('admin.curriculum_pillars.fields.icon'))
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(asset('images/logo.webp')),
                TextColumn::make('title_display')
                    ->label(__('admin.curriculum_pillars.fields.title'))
                    ->state(fn (CurriculumPillar $record): string => $record->trans('title'))
                    ->weight('bold')
                    ->searchable(query: fn ($query, $search) => $query->where('title', 'ilike', "%{$search}%")),
                TextColumn::make('color_theme')
                    ->label(__('admin.curriculum_pillars.fields.color_theme'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'red' => 'danger',
                        'green', 'emerald' => 'success',
                        'blue' => 'info',
                        'amber' => 'warning',
                        'purple' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __('admin.curriculum_pillars.themes_short.'.$state) ?: ucfirst($state)),
                IconColumn::make('is_active')
                    ->label(__('admin.fields.active'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->sortable(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurriculumPillars::route('/'),
            'create' => CreateCurriculumPillar::route('/create'),
            'edit' => EditCurriculumPillar::route('/{record}/edit'),
        ];
    }
}
