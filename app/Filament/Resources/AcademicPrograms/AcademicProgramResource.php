<?php

namespace App\Filament\Resources\AcademicPrograms;

use App\Filament\Resources\AcademicPrograms\Pages\CreateAcademicProgram;
use App\Filament\Resources\AcademicPrograms\Pages\EditAcademicProgram;
use App\Filament\Resources\AcademicPrograms\Pages\ListAcademicPrograms;
use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\AcademicProgram;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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

class AcademicProgramResource extends CmsResource
{
    protected static ?string $model = AcademicProgram::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make(__('admin.academic_programs.sections.content'))
                            ->description(__('admin.academic_programs.sections.content_desc'))
                            ->schema([
                                Tabs::make('ProgramTranslations')
                                    ->tabs([
                                        Tab::make('en')
                                            ->label(__('admin.academic_programs.tabs.en'))
                                            ->schema([
                                                TextInput::make('label.en')
                                                    ->label(__('admin.academic_programs.fields.label_en'))
                                                    ->placeholder(__('admin.academic_programs.fields.label_placeholder_en'))
                                                    ->required()
                                                    ->maxLength(100),
                                                TextInput::make('title.en')
                                                    ->label(__('admin.academic_programs.fields.title_en'))
                                                    ->placeholder(__('admin.academic_programs.fields.title_placeholder_en'))
                                                    ->required()
                                                    ->maxLength(150),
                                                Textarea::make('description.en')
                                                    ->label(__('admin.academic_programs.fields.description_en'))
                                                    ->placeholder(__('admin.academic_programs.fields.description_placeholder_en'))
                                                    ->rows(4)
                                                    ->required()
                                                    ->maxLength(500),
                                            ]),

                                        Tab::make('id')
                                            ->label(__('admin.academic_programs.tabs.id'))
                                            ->schema([
                                                TextInput::make('label.id')
                                                    ->label(__('admin.academic_programs.fields.label_id'))
                                                    ->placeholder(__('admin.academic_programs.fields.label_placeholder_id'))
                                                    ->required()
                                                    ->maxLength(100),
                                                TextInput::make('title.id')
                                                    ->label(__('admin.academic_programs.fields.title_id'))
                                                    ->placeholder(__('admin.academic_programs.fields.title_placeholder_id'))
                                                    ->required()
                                                    ->maxLength(150),
                                                Textarea::make('description.id')
                                                    ->label(__('admin.academic_programs.fields.description_id'))
                                                    ->placeholder(__('admin.academic_programs.fields.description_placeholder_id'))
                                                    ->rows(4)
                                                    ->required()
                                                    ->maxLength(500),
                                            ]),

                                        Tab::make('zh-TW')
                                            ->label(__('admin.academic_programs.tabs.zh'))
                                            ->schema([
                                                TextInput::make('label.zh-TW')
                                                    ->label(__('admin.academic_programs.fields.label_zh'))
                                                    ->placeholder(__('admin.academic_programs.fields.label_placeholder_zh'))
                                                    ->required()
                                                    ->maxLength(100),
                                                TextInput::make('title.zh-TW')
                                                    ->label(__('admin.academic_programs.fields.title_zh'))
                                                    ->placeholder(__('admin.academic_programs.fields.title_placeholder_zh'))
                                                    ->required()
                                                    ->maxLength(150),
                                                Textarea::make('description.zh-TW')
                                                    ->label(__('admin.academic_programs.fields.description_zh'))
                                                    ->placeholder(__('admin.academic_programs.fields.description_placeholder_zh'))
                                                    ->rows(4)
                                                    ->required()
                                                    ->maxLength(500),
                                            ]),
                                    ]),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 1])
                    ->schema([
                        Section::make(__('admin.academic_programs.sections.media_settings'))
                            ->description(__('admin.academic_programs.sections.media_settings_desc'))
                            ->schema([
                                CmsFields::image('image', 4096, __('admin.academic_programs.fields.image'))
                                    ->label(__('admin.academic_programs.fields.image'))
                                    ->helperText(__('admin.academic_programs.fields.image_helper')),

                                CmsFields::safeUrl('url', __('admin.academic_programs.fields.url'))
                                    ->label(__('admin.academic_programs.fields.url'))
                                    ->default('/academics')
                                    ->helperText(__('admin.academic_programs.fields.url_helper')),

                                TextInput::make('sort_order')
                                    ->label(__('admin.fields.sort_order'))
                                    ->numeric()
                                    ->default(0)
                                    ->helperText(__('admin.academic_programs.fields.sort_order_helper')),

                                Toggle::make('is_active')
                                    ->label(__('admin.fields.active'))
                                    ->default(true)
                                    ->helperText(__('admin.academic_programs.fields.is_active_helper')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label(__('admin.academic_programs.fields.image'))
                    ->square()
                    ->size(50)
                    ->defaultImageUrl(asset('images/logo.webp')),
                TextColumn::make('label_display')
                    ->label(__('admin.academic_programs.fields.label'))
                    ->state(fn (AcademicProgram $record): string => $record->trans('label'))
                    ->badge()
                    ->color('warning'),
                TextColumn::make('title_display')
                    ->label(__('admin.academic_programs.fields.title'))
                    ->state(fn (AcademicProgram $record): string => $record->trans('title'))
                    ->weight('bold')
                    ->searchable(query: fn ($query, $search) => $query->where('title', 'ilike', "%{$search}%")),
                TextColumn::make('url')
                    ->label(__('admin.academic_programs.fields.url'))
                    ->limit(25)
                    ->color('gray'),
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
            'index' => ListAcademicPrograms::route('/'),
            'create' => CreateAcademicProgram::route('/create'),
            'edit' => EditAcademicProgram::route('/{record}/edit'),
        ];
    }
}
