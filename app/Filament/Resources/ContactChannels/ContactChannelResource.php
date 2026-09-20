<?php

namespace App\Filament\Resources\ContactChannels;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\ContactChannel;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactChannelResource extends CmsResource
{
    protected static ?string $model = ContactChannel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Contacts & QR';

    protected static ?string $pluralModelLabel = 'Contacts & QR';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Saluran Kontak')
                        ->description('Pilih tipe saluran dan masukkan informasi kontak sekolah')
                        ->schema([
                            Select::make('channel')
                                ->label('Tipe Saluran')
                                ->options([
                                    'admission_tel' => 'Admission Telephone',
                                    'phone' => 'Telephone Sekolah',
                                    'instagram' => 'Instagram',
                                    'whatsapp' => 'WhatsApp',
                                    'tiktok' => 'TikTok',
                                    'address' => 'Alamat Sekolah',
                                    'email' => 'Email',
                                ])
                                ->required()
                                ->unique(ignoreRecord: true),
                            TextInput::make('label')
                                ->label('Label Teks')
                                ->placeholder('Contoh: Telepon Admisi / @jakartataipeischool')
                                ->required()
                                ->maxLength(150),
                            TextInput::make('value')
                                ->label('Nilai / Tautan')
                                ->placeholder('Nomor telp, link sosmed, atau alamat')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Section::make('Gambar QR Code')
                        ->description('QR Code untuk pemindaian cepat (WhatsApp, Formulir Pendaftaran, dll.)')
                        ->schema([
                            CmsFields::image('qr_image', 1024, 'Gambar QR Code (Opsional)'),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Penempatan & Visibilitas')
                        ->description('Atur lokasi tampilan kontak pada website publik')
                        ->schema([
                            Toggle::make('is_visible_header')
                                ->label('Tampilkan di Header Navbar')
                                ->default(false),
                            Toggle::make('is_visible_footer')
                                ->label('Tampilkan di Footer')
                                ->default(true),
                            TextInput::make('sort_order')
                                ->label('Urutan Tampilan')
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('channel')
                    ->label('Saluran')
                    ->badge(),
                TextColumn::make('label')
                    ->label('Label')
                    ->weight('medium'),
                TextColumn::make('value')
                    ->label('Nilai Kontak')
                    ->limit(55)
                    ->searchable(),
                IconColumn::make('is_visible_header')
                    ->label('Header')
                    ->boolean(),
                IconColumn::make('is_visible_footer')
                    ->label('Footer')
                    ->boolean(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactChannels::route('/'),
            'create' => Pages\CreateContactChannel::route('/create'),
            'edit' => Pages\EditContactChannel::route('/{record}/edit'),
        ];
    }
}
