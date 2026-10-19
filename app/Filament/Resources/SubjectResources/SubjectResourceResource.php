<?php

namespace App\Filament\Resources\SubjectResources;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\SubjectResource;
use App\Models\SubjectResourceType;
use App\Support\DepartmentPermission;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Section::make(__('admin.sections.material_type'))
                    ->schema([
                        Select::make('type_id')
                            ->label(__('admin.labels.type_material'))
                            ->relationship('type', 'name', modifyQueryUsing: function (Builder $query) {
                                $allowed = DepartmentPermission::getAllowedSubjectResourceTypes(auth()->user());
                                if ($allowed !== null) {
                                    $query->whereIn('code', $allowed);
                                }

                                return $query;
                            })
                            ->default(function () {
                                $allowed = DepartmentPermission::getAllowedSubjectResourceTypes(auth()->user());
                                if ($allowed !== null && count($allowed) === 1) {
                                    return SubjectResourceType::where('code', $allowed[0])->value('id');
                                }

                                return null;
                            })
                            ->required()
                            ->live()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columnSpanFull(),
                Group::make()->schema([
                    Section::make(__('admin.sections.subject_main_document'))
                        ->schema([
                            TextInput::make('topic')
                                ->label(__('admin.labels.topic_material_name'))
                                ->required()
                                ->maxLength(191),
                            CmsFields::image('image', 4096, __('admin.labels.subject_image'))
                                ->visible(fn (Get $get): bool => self::isWeeklyNews((int) $get('type_id'))),
                            CmsFields::document(true, __('admin.labels.subject_document')),
                        ]),

                    Section::make(__('admin.sections.title_translations'))
                        ->schema([
                            CmsFields::translations(__('admin.labels.subject_title'),
                                showSummary: fn (Get $get): bool => self::isWeeklyNews((int) $get('../../type_id')),
                                showArticle: false),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    CmsFields::publishing(),

                    Section::make(__('admin.sections.material_classification'))
                        ->description(__('admin.descriptions.material_classification'))
                        ->schema([
                            CmsFields::department(),
                            CmsFields::schoolLevels()
                                ->visible(fn (Get $get): bool => ! self::isDetailedType((int) $get('type_id'))),
                            Select::make('class_level')
                                ->label(__('admin.fields.class'))
                                ->options(SubjectResource::CLASS_LEVEL_OPTIONS)
                                ->required(fn (Get $get): bool => self::isDetailedType((int) $get('type_id')))
                                ->visible(fn (Get $get): bool => self::isDetailedType((int) $get('type_id')))
                                ->searchable(),
                            Select::make('semester')
                                ->label(__('admin.fields.semester'))
                                ->options(SubjectResource::SEMESTER_OPTIONS)
                                ->required(fn (Get $get): bool => self::isDetailedType((int) $get('type_id')))
                                ->visible(fn (Get $get): bool => self::isDetailedType((int) $get('type_id'))),
                            CmsFields::uploadDate(),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('topic')
                    ->label(__('admin.labels.topic_material'))
                    ->searchable()
                    ->weight('medium')
                    ->limit(40),
                TextColumn::make('type.name')
                    ->label(__('admin.labels.type_material'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('school_level_label')
                    ->label(__('admin.fields.school_level'))
                    ->formatStateUsing(fn (SubjectResource $record): ?string => $record->usesDetailedAcademicPeriod() ? null : $record->school_level_label)
                    ->placeholder('-'),
                TextColumn::make('resource_level_label')
                    ->label(__('admin.fields.class_semester'))
                    ->placeholder('-'),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && DepartmentPermission::isDepartmentRole($user->role?->code)) {
            $allowedTypes = DepartmentPermission::getAllowedSubjectResourceTypes($user);
            if ($allowedTypes !== null) {
                $query->whereHas('type', fn (Builder $q) => $q->whereIn('code', $allowedTypes));
            }
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjectResources::route('/'),
            'create' => Pages\CreateSubjectResource::route('/create'),
            'edit' => Pages\EditSubjectResource::route('/{record}/edit'),
        ];
    }

    private static function isDetailedType(int $typeId): bool
    {
        return in_array($typeId, SubjectResource::detailedTypeIds(), true);
    }

    private static function isWeeklyNews(int $typeId): bool
    {
        return SubjectResourceType::whereKey($typeId)->where('code', 'weekly_news')->exists();
    }
}
