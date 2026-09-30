<?php

namespace App\Filament\Resources\SubjectResources;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\SubjectResource;
use App\Models\SubjectResourceType;
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
                Section::make('Jenis Materi')
                    ->schema([
                        Select::make('type_id')
                            ->label('Tipe / Jenis Materi')
                            ->relationship('type', 'name')
                            ->required()
                            ->live()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columnSpanFull(),
                Group::make()->schema([
                    Section::make('Materi & Dokumen Utama')
                        ->schema([
                            TextInput::make('topic')
                                ->label('Topik / Nama Materi')
                                ->required()
                                ->maxLength(191),
                            CmsFields::image('image', 4096, 'Gambar Cover Weekly News')
                                ->visible(fn (Get $get): bool => self::isWeeklyNews((int) $get('type_id'))),
                            CmsFields::document(true, 'File Dokumen Materi (PDF, DOCX, RAR)'),
                        ]),

                    Section::make('Judul & Terjemahan')
                        ->schema([
                            CmsFields::translations('Judul Materi',
                                showSummary: fn (Get $get): bool => self::isWeeklyNews((int) $get('../../type_id')),
                                showArticle: false),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    CmsFields::publishing(),

                    Section::make('Klasifikasi Materi')
                        ->description('Tipe berkas, departemen, dan jenjang sasaran')
                        ->schema([
                            CmsFields::department(),
                            CmsFields::schoolLevels()
                                ->visible(fn (Get $get): bool => ! self::isDetailedType((int) $get('type_id'))),
                            Select::make('class_level')
                                ->label('Kelas')
                                ->options(SubjectResource::CLASS_LEVEL_OPTIONS)
                                ->required(fn (Get $get): bool => self::isDetailedType((int) $get('type_id')))
                                ->visible(fn (Get $get): bool => self::isDetailedType((int) $get('type_id')))
                                ->searchable(),
                            Select::make('semester')
                                ->label('Semester')
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
                    ->label('Topik Materi')
                    ->searchable()
                    ->weight('medium')
                    ->limit(40),
                TextColumn::make('type.name')
                    ->label('Tipe Materi')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('school_level_label')
                    ->label('Jenjang')
                    ->formatStateUsing(fn (SubjectResource $record): ?string => $record->usesDetailedAcademicPeriod() ? null : $record->school_level_label)
                    ->placeholder('-'),
                TextColumn::make('resource_level_label')
                    ->label('Kelas / Semester')
                    ->placeholder('-'),
                TextColumn::make('department.name')
                    ->label('Departemen'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('upload_date')
                    ->label('Tanggal Unggah')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('translation_status')
                    ->label('Terjemahan')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
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
