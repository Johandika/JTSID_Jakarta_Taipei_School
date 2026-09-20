<?php

namespace App\Filament\Resources\SubjectResources;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\SubjectResource;
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
                Group::make()->schema([
                    Section::make('Materi & Dokumen Utama')
                        ->description('Judul materi pembelajaran dan file berkas unduhan')
                        ->schema([
                            TextInput::make('topic')
                                ->label('Topik / Nama Materi')
                                ->required()
                                ->maxLength(191),
                            CmsFields::document(true, 'File Dokumen Materi (PDF, DOCX, RAR)'),
                        ]),

                    Section::make('Deskripsi & Terjemahan')
                        ->description('Instruksi dan deskripsi materi dalam berbagai bahasa')
                        ->schema([
                            CmsFields::translations('Judul Materi'),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    CmsFields::publishing(),

                    Section::make('Klasifikasi Materi')
                        ->description('Tipe berkas, departemen, dan jenjang sasaran')
                        ->schema([
                            Select::make('type_id')
                                ->label('Tipe / Jenis Materi')
                                ->relationship('type', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            CmsFields::department(),
                            CmsFields::schoolLevels(),
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
                    ->label('Jenjang'),
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
}
