<?php

namespace App\Filament\Resources\AnnouncementPopups;

use App\Filament\Resources\AnnouncementPopups\Pages\CreateAnnouncementPopup;
use App\Filament\Resources\AnnouncementPopups\Pages\EditAnnouncementPopup;
use App\Filament\Resources\AnnouncementPopups\Pages\ListAnnouncementPopups;
use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\AnnouncementPopup;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementPopupResource extends CmsResource
{
    protected static ?string $model = AnnouncementPopup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Announcement Popups';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Konten & Pesan Pengumuman')
                        ->description('Judul, isi pesan popup, dan poster visual')
                        ->schema([
                            TextInput::make('title')
                                ->label('Judul Pengumuman')
                                ->required()
                                ->maxLength(191),
                            Textarea::make('body')
                                ->label('Pesan Lengkap')
                                ->rows(4)
                                ->maxLength(5000)
                                ->columnSpanFull(),
                            CmsFields::image('image', 4096, 'Poster Visual Pengumuman (Opsional)')
                                ->helperText('Gambar poster ucapan selamat, event, atau informasi mendesak.'),
                            CmsFields::safeUrl('link_url', 'Tautan Baca Selengkapnya'),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Jadwal & Status Penayangan')
                        ->description('Atur waktu aktif popup di website')
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Aktifkan Popup')
                                ->default(false)
                                ->helperText('Aktifkan saat popup sudah siap tampil kepada pengunjung.'),
                            DateTimePicker::make('starts_at')
                                ->seconds(false)
                                ->label('Waktu Mulai ('.config('app.timezone').')'),
                            DateTimePicker::make('ends_at')
                                ->seconds(false)
                                ->after(fn (Get $get): ?string => filled($get('starts_at')) ? 'starts_at' : null)
                                ->label('Waktu Selesai ('.config('app.timezone').')'),
                            TextInput::make('sort_order')
                                ->label('Prioritas Tampilan')
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required()
                                ->helperText('Angka lebih kecil tampil lebih dulu.'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Pengumuman')
                    ->searchable()
                    ->weight('medium'),
                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),
                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime('d M Y, H:i'),
                TextColumn::make('ends_at')
                    ->label('Selesai')
                    ->dateTime('d M Y, H:i'),
                TextColumn::make('sort_order')
                    ->label('Prioritas')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncementPopups::route('/'),
            'create' => CreateAnnouncementPopup::route('/create'),
            'edit' => EditAnnouncementPopup::route('/{record}/edit'),
        ];
    }
}
