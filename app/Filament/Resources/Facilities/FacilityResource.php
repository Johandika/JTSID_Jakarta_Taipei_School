<?php

namespace App\Filament\Resources\Facilities;

use App\Filament\Resources\CmsResource;
use App\Filament\Resources\Facilities\Pages\CreateFacility;
use App\Filament\Resources\Facilities\Pages\EditFacility;
use App\Filament\Resources\Facilities\Pages\ListFacilities;
use App\Filament\Support\CmsFields;
use App\Models\Facility;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FacilityResource extends CmsResource
{
    protected static ?string $model = Facility::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $navigationLabel = 'Fasilitas';

    protected static ?string $pluralModelLabel = 'Fasilitas';

    protected static ?string $modelLabel = 'Fasilitas';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make('Nama Fasilitas')
                            ->description('Nama fasilitas dalam 3 bahasa (Indonesia, Inggris, dan Mandarin)')
                            ->schema([
                                Tabs::make('FacilityTranslations')
                                    ->tabs([
                                        Tab::make('id')
                                            ->label('🇮🇩 Indonesian (Bahasa Indonesia)')
                                            ->schema([
                                                TextInput::make('name.id')
                                                    ->label('Nama Fasilitas (ID)')
                                                    ->placeholder('cth. Ruang Kelas / Perpustakaan')
                                                    ->required()
                                                    ->maxLength(150),
                                            ]),
                                        Tab::make('en')
                                            ->label('🇬🇧 English (Primary)')
                                            ->schema([
                                                TextInput::make('name.en')
                                                    ->label('Facility Name (EN)')
                                                    ->placeholder('e.g. Classroom / Library')
                                                    ->required()
                                                    ->maxLength(150),
                                            ]),
                                        Tab::make('zh-TW')
                                            ->label('🇹🇼 Traditional Chinese (繁體中文)')
                                            ->schema([
                                                TextInput::make('name.zh-TW')
                                                    ->label('Facility Name (中文)')
                                                    ->placeholder('例：教室 / 圖書館')
                                                    ->required()
                                                    ->maxLength(150),
                                            ]),
                                    ]),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 1])
                    ->schema([
                        Section::make('Kategori & Foto')
                            ->description('Pilih kategori dan unggah foto fasilitas')
                            ->schema([
                                Select::make('category')
                                    ->label('Kategori Fasilitas')
                                    ->options(Facility::getCategoryOptions())
                                    ->required()
                                    ->native(false)
                                    ->helperText('Pilih salah satu: Fasilitas Sekolah, Asrama Guru, TK, SD, SMP, SMA.'),

                                CmsFields::image('image', 4096, 'Foto Fasilitas')
                                    ->required()
                                    ->helperText('Format JPG, PNG, atau WebP. Maksimal 4MB.'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->square()
                    ->size(60),
                TextColumn::make('name_display')
                    ->label('Nama Fasilitas')
                    ->state(fn (Facility $record): string => $record->trans('name'))
                    ->weight('bold')
                    ->searchable(query: fn ($query, $search) => $query->where('name', 'ilike', "%{$search}%")),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(fn (string $state): string => Facility::getCategoryLabel($state))
                    ->badge()
                    ->color('primary'),
                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Ditambahkan')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(Facility::getCategoryOptions()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFacilities::route('/'),
            'create' => CreateFacility::route('/create'),
            'edit' => EditFacility::route('/{record}/edit'),
        ];
    }
}
