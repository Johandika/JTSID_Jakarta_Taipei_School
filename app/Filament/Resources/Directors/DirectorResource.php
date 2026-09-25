<?php

namespace App\Filament\Resources\Directors;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\Director;
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

class DirectorResource extends CmsResource
{
    protected static ?string $model = Director::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $navigationLabel = 'Dewan Pengurus';

    protected static ?string $pluralModelLabel = 'Dewan Pengurus';

    protected static ?string $modelLabel = 'Anggota Dewan Pengurus';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Anggota Dewan Pengurus')
                        ->description('Data profil trilingual (Indonesia, Inggris, dan Mandarin)')
                        ->schema([
                            Tabs::make('DirectorTranslations')
                                ->tabs([
                                    Tab::make('id')
                                        ->label('🇮🇩 Indonesian (Bahasa Indonesia)')
                                        ->schema([
                                            TextInput::make('name.id')
                                                ->label('Nama Lengkap (ID)')
                                                ->placeholder('cth. Dr. Olivia Chen, Ph.D.')
                                                ->required()
                                                ->maxLength(150),
                                            TextInput::make('role.id')
                                                ->label('Jabatan / Posisi (ID)')
                                                ->placeholder('cth. Ketua Dewan Pengurus')
                                                ->required()
                                                ->maxLength(150),
                                            Textarea::make('education.id')
                                                ->label('Riwayat Pendidikan (ID)')
                                                ->placeholder('Satu gelar/kampus per baris...')
                                                ->rows(3)
                                                ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : (string) ($state ?? ''))
                                                ->dehydrateStateUsing(fn ($state) => is_string($state) ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $state)))) : (array) ($state ?? []))
                                                ->helperText('Tuliskan satu riwayat pendidikan per baris (tekan Enter untuk baris baru)'),
                                            Textarea::make('bio.id')
                                                ->label('Profil & Pengalaman (ID)')
                                                ->placeholder('Deskripsi singkat pengalaman atau dedikasi kepemimpinan...')
                                                ->rows(3)
                                                ->maxLength(1000),
                                        ]),

                                    Tab::make('en')
                                        ->label('🇬🇧 English')
                                        ->schema([
                                            TextInput::make('name.en')
                                                ->label('Full Name (EN)')
                                                ->placeholder('e.g. Dr. Olivia Chen, Ph.D.')
                                                ->maxLength(150),
                                            TextInput::make('role.en')
                                                ->label('Position / Role (EN)')
                                                ->placeholder('e.g. Chairman of the Board')
                                                ->maxLength(150),
                                            Textarea::make('education.en')
                                                ->label('Education Background (EN)')
                                                ->placeholder('One degree/alma mater per line...')
                                                ->rows(3)
                                                ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : (string) ($state ?? ''))
                                                ->dehydrateStateUsing(fn ($state) => is_string($state) ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $state)))) : (array) ($state ?? []))
                                                ->helperText('Enter one education degree per line'),
                                            Textarea::make('bio.en')
                                                ->label('Profile & Experience (EN)')
                                                ->placeholder('Brief overview of leadership expertise and contribution...')
                                                ->rows(3)
                                                ->maxLength(1000),
                                        ]),

                                    Tab::make('zh-TW')
                                        ->label('🇹🇼 Traditional Chinese (繁體中文)')
                                        ->schema([
                                            TextInput::make('name.zh-TW')
                                                ->label('姓名 (中文)')
                                                ->placeholder('例：陳美玲 博士')
                                                ->maxLength(150),
                                            TextInput::make('role.zh-TW')
                                                ->label('職位 / 職銜 (中文)')
                                                ->placeholder('例：董事長')
                                                ->maxLength(150),
                                            Textarea::make('education.zh-TW')
                                                ->label('學歷背景 (中文)')
                                                ->placeholder('每行一筆學位或畢業院校...')
                                                ->rows(3)
                                                ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : (string) ($state ?? ''))
                                                ->dehydrateStateUsing(fn ($state) => is_string($state) ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $state)))) : (array) ($state ?? []))
                                                ->helperText('每行輸入一筆學歷（換行分隔）'),
                                            Textarea::make('bio.zh-TW')
                                                ->label('經歷與專長 (中文)')
                                                ->placeholder('簡短介紹校務領導經歷與專長貢獻...')
                                                ->rows(3)
                                                ->maxLength(1000),
                                        ]),
                                ]),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Foto Profil')
                        ->description('Foto portrait studio')
                        ->schema([
                            CmsFields::image('photo', 3072, 'Foto Profil')
                                ->helperText('Disarankan foto portrait monokrom (min. 600x600 px, maks. 3MB)'),
                        ]),

                    Section::make('Status & Urutan')
                        ->schema([
                            TextInput::make('sort_order')
                                ->label('Urutan Tampilan')
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                            Toggle::make('is_active')
                                ->label('Aktif / Tampilkan di Website')
                                ->default(true),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->state(fn (Director $record): string => $record->photo_url)
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->state(fn (Director $record): string => $record->trans('name'))
                    ->searchable(query: function ($query, string $search) {
                        return $query->where('name', 'ilike', '%'.$search.'%');
                    })
                    ->weight('medium'),
                TextColumn::make('role')
                    ->label('Jabatan')
                    ->state(fn (Director $record): string => $record->trans('role')),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDirectors::route('/'),
            'create' => Pages\CreateDirector::route('/create'),
            'edit' => Pages\EditDirector::route('/{record}/edit'),
        ];
    }
}
