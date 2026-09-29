<?php

namespace App\Filament\Resources\HeroSlides;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\HeroSlide;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeroSlideResource extends CmsResource
{
    protected static ?string $model = HeroSlide::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Hero Slides';

    protected static ?string $pluralModelLabel = 'Hero Slides';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Media Latar Hero (Foto / Video)')
                        ->description('Wajib diisi salah satu: Foto resolusi tinggi ATAU Link Video YouTube')
                        ->schema([
                            CmsFields::image('image', 4096, 'Foto / Gambar Hero')
                                ->label('Gambar Slide Hero')
                                ->helperText('Wajib diunggah jika tidak mengisi Link Video YouTube. Format: JPG, PNG, WebP (maks. 4MB). Jika URL video diisi, gambar ini opsional sebagai poster cadangan.')
                                ->rules([
                                    fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                        if (blank($value) && blank($get('video_url'))) {
                                            $fail('Wajib mengisi minimal salah satu: Gambar Slide Hero atau URL Video YouTube.');
                                        }
                                    },
                                ]),

                            TextInput::make('video_url')
                                ->label('URL Video (YouTube atau File MP4)')
                                ->placeholder('https://www.youtube.com/watch?v=... atau https://domain.com/video.mp4')
                                ->maxLength(500)
                                ->helperText('Wajib diisi jika tidak mengunggah Gambar. Mendukung link YouTube atau file video langsung (.mp4). Video akan otomatis diputar tanpa suara (muted & loop) di latar belakang.')
                                ->url()
                                ->rules([
                                    fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                        if (blank($value) && blank($get('image'))) {
                                            $fail('Wajib mengisi minimal salah satu: Gambar Slide Hero atau URL Video.');
                                        }
                                    },
                                ]),
                        ]),

                    Section::make('Konten Teks & Shape Panel')
                        ->description('Pengaturan tampilan teks dan shape hijau diagonal di atas latar hero')
                        ->schema([
                            Toggle::make('show_text')
                                ->label('Tampilkan Teks & Shape Panel Hijau')
                                ->default(true)
                                ->live()
                                ->helperText('Jika dinonaktifkan, shape hijau diagonal beserta judul, deskripsi, dan tombol CTA tidak ditampilkan (latar video/foto tampil bersih 100% full-screen).'),

                            Tabs::make('SlideTranslations')
                                ->visible(fn (Get $get): bool => (bool) ($get('show_text') ?? true))
                                ->tabs([
                                    Tab::make('en')
                                        ->label('🇬🇧 English (Primary)')
                                        ->schema([
                                            TextInput::make('tagline.en')
                                                ->label('Tagline / Eyebrow (EN)')
                                                ->placeholder('e.g. Jakarta Taipei School')
                                                ->maxLength(100),
                                            TextInput::make('title.en')
                                                ->label('Judul / Headline Utama (EN)')
                                                ->placeholder('e.g. Excellence in Trilingual Education')
                                                ->required(fn (Get $get): bool => (bool) ($get('show_text') ?? true))
                                                ->maxLength(191),
                                            Textarea::make('description.en')
                                                ->label('Deskripsi Singkat (EN)')
                                                ->placeholder('Brief introduction or summary...')
                                                ->rows(3)
                                                ->maxLength(500),
                                            TextInput::make('cta_text.en')
                                                ->label('Teks Tombol CTA (EN)')
                                                ->placeholder('e.g. Apply Now')
                                                ->maxLength(100),
                                        ]),

                                    Tab::make('id')
                                        ->label('🇮🇩 Indonesian (Bahasa Indonesia)')
                                        ->schema([
                                            TextInput::make('tagline.id')
                                                ->label('Tagline / Eyebrow (ID)')
                                                ->placeholder('cth. Jakarta Taipei School')
                                                ->maxLength(100),
                                            TextInput::make('title.id')
                                                ->label('Judul / Headline Utama (ID)')
                                                ->placeholder('cth. Keunggulan Pendidikan Tiga Bahasa')
                                                ->maxLength(191),
                                            Textarea::make('description.id')
                                                ->label('Deskripsi Singkat (ID)')
                                                ->placeholder('Ringkasan atau pengantar singkat...')
                                                ->rows(3)
                                                ->maxLength(500),
                                            TextInput::make('cta_text.id')
                                                ->label('Teks Tombol CTA (ID)')
                                                ->placeholder('cth. Daftar Sekarang')
                                                ->maxLength(100),
                                        ]),

                                    Tab::make('zh-TW')
                                        ->label('🇹🇼 Traditional Chinese (繁體中文)')
                                        ->schema([
                                            TextInput::make('tagline.zh-TW')
                                                ->label('Tagline / Eyebrow (中文)')
                                                ->placeholder('例：雅加達臺北學校')
                                                ->maxLength(100),
                                            TextInput::make('title.zh-TW')
                                                ->label('Judul / Headline Utama (中文)')
                                                ->placeholder('例：深耕三語，開創卓越教育')
                                                ->maxLength(191),
                                            Textarea::make('description.zh-TW')
                                                ->label('Deskripsi Singkat (中文)')
                                                ->placeholder('簡短介紹或摘要...')
                                                ->rows(3)
                                                ->maxLength(500),
                                            TextInput::make('cta_text.zh-TW')
                                                ->label('Teks Tombol CTA (中文)')
                                                ->placeholder('例：立即報名')
                                                ->maxLength(100),
                                        ]),
                                ]),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Link Tombol CTA')
                        ->description('URL tujuan saat tombol aksi diklik')
                        ->visible(fn (Get $get): bool => (bool) ($get('show_text') ?? true))
                        ->schema([
                            CmsFields::safeUrl('cta_url', 'Link URL Tujuan CTA')
                                ->label('Link URL CTA')
                                ->placeholder('/admissions atau https://...')
                                ->helperText('Relative path internal (/admissions, /school-profile, /academics) atau URL eksternal.'),
                        ]),

                    Section::make('Status & Urutan Tampilan')
                        ->schema([
                            TextInput::make('sort_order')
                                ->label('Urutan Tampilan')
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required()
                                ->helperText('Nomor urut rotasi carousel (0, 1, 2, ...)'),
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
                ImageColumn::make('image')
                    ->label('Foto / Poster')
                    ->state(fn (HeroSlide $record): ?string => $record->getImageOrPosterUrl())
                    ->width(80)
                    ->height(50),
                TextColumn::make('title.en')
                    ->label('Judul Headline (EN)')
                    ->searchable()
                    ->weight('medium')
                    ->placeholder('(Tanpa Teks / Full Media)')
                    ->limit(40),
                IconColumn::make('video_url')
                    ->label('Video')
                    ->boolean()
                    ->state(fn (HeroSlide $record): bool => $record->hasVideo())
                    ->trueIcon('heroicon-o-video-camera')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                IconColumn::make('show_text')
                    ->label('Shape & Teks')
                    ->boolean()
                    ->trueIcon('heroicon-o-chat-bubble-bottom-center-text')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('tagline.en')
                    ->label('Tagline')
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }
}
