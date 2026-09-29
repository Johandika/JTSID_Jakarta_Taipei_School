<?php

namespace App\Filament\Resources\MandarinPrograms;

use App\Filament\Resources\CmsResource;
use App\Filament\Resources\MandarinPrograms\Pages\CreateMandarinProgram;
use App\Filament\Resources\MandarinPrograms\Pages\EditMandarinProgram;
use App\Filament\Resources\MandarinPrograms\Pages\ListMandarinPrograms;
use App\Models\MandarinProgram;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MandarinProgramResource extends CmsResource
{
    protected static ?string $model = MandarinProgram::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $navigationLabel = 'Short-Term Mandarin Programs';

    protected static ?string $pluralModelLabel = 'Short-Term Mandarin Programs';

    protected static ?string $modelLabel = 'Mandarin Program';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Utama Program (Trilingual)')
                        ->description('Judul, ringkasan, dan penjelasan lengkap program')
                        ->schema([
                            Tabs::make('ProgramTranslations')
                                ->tabs([
                                    Tab::make('id')
                                        ->label('🇮🇩 Indonesian')
                                        ->schema([
                                            TextInput::make('title.id')
                                                ->label('Judul Program (ID)')
                                                ->placeholder('cth. Mandarin Classes / Workshop Kaligrafi')
                                                ->required()
                                                ->maxLength(191),
                                            Textarea::make('short_description.id')
                                                ->label('Deskripsi Singkat (ID)')
                                                ->placeholder('Deskripsi ringkas yang tampil pada kartu program...')
                                                ->rows(2)
                                                ->maxLength(300),
                                            Textarea::make('description.id')
                                                ->label('About the Program / Penjelasan Lengkap (ID)')
                                                ->placeholder('Penjelasan lengkap mengenai tujuan, silabus, dan materi program...')
                                                ->rows(4),
                                        ]),

                                    Tab::make('en')
                                        ->label('🇬🇧 English')
                                        ->schema([
                                            TextInput::make('title.en')
                                                ->label('Program Title (EN)')
                                                ->placeholder('e.g. Mandarin Classes')
                                                ->maxLength(191),
                                            Textarea::make('short_description.en')
                                                ->label('Short Description (EN)')
                                                ->placeholder('Short summary displayed on program card...')
                                                ->rows(2)
                                                ->maxLength(300),
                                            Textarea::make('description.en')
                                                ->label('About the Program (EN)')
                                                ->placeholder('Detailed information about the program...')
                                                ->rows(4),
                                        ]),

                                    Tab::make('zh-TW')
                                        ->label('🇹🇼 Traditional Chinese')
                                        ->schema([
                                            TextInput::make('title.zh-TW')
                                                ->label('課程名稱 (繁中)')
                                                ->placeholder('例如：華語班 / 華語文能力測驗')
                                                ->maxLength(191),
                                            Textarea::make('short_description.zh-TW')
                                                ->label('簡要說明 (繁中)')
                                                ->placeholder('卡片簡述...')
                                                ->rows(2)
                                                ->maxLength(300),
                                            Textarea::make('description.zh-TW')
                                                ->label('課程詳情介紹 (繁中)')
                                                ->placeholder('完整課程規劃與內容說明...')
                                                ->rows(4),
                                        ]),
                                ]),
                        ]),

                    Section::make('Jadwal & Periode Program')
                        ->description('Tanggal pelaksanaan kegiatan atau tes')
                        ->schema([
                            TextInput::make('program_date_label.id')
                                ->label('Label Tanggal (ID)')
                                ->placeholder('cth. Program Period, Test Date, Program Date')
                                ->maxLength(60),
                            TextInput::make('program_date_text.id')
                                ->label('Teks Tanggal Custom (ID)')
                                ->placeholder('cth. Nov 2025 – Dec 2025 atau 04 Oct 2025')
                                ->helperText('Jika diisi, teks ini akan ditampilkan langsung.')
                                ->maxLength(100),
                            DatePicker::make('program_start_date')
                                ->label('Tanggal Mulai Program')
                                ->native(false),
                            DatePicker::make('program_end_date')
                                ->label('Tanggal Selesai Program')
                                ->native(false),
                        ])
                        ->columns(2),

                    Section::make('Periode & Tautan Pendaftaran')
                        ->description('Informasi registrasi dan link website pendaftaran')
                        ->schema([
                            TextInput::make('registration_date_text.id')
                                ->label('Teks Periode Registrasi Custom')
                                ->placeholder('cth. 09 Sep 2025 – 30 Sep 2025')
                                ->columnSpanFull()
                                ->maxLength(100),
                            DatePicker::make('registration_start_date')
                                ->label('Tanggal Buka Registrasi')
                                ->native(false),
                            DatePicker::make('registration_end_date')
                                ->label('Tanggal Tutup Registrasi')
                                ->native(false),
                            TextInput::make('registration_url')
                                ->label('URL Link Pendaftaran (Registration URL)')
                                ->placeholder('https:// atau link formulir online')
                                ->url()
                                ->columnSpanFull()
                                ->helperText('Jika diisi dan registrasi dibuka, tombol "Registration Now" akan aktif membuka link ini di tab baru.'),
                        ])
                        ->columns(2),

                    Section::make('Informasi Tambahan / Levels / Jadwal Sesi (Fleksibel)')
                        ->description('Contoh: Level 1 - 4 untuk kelas Mandarin, atau jadwal sesi untuk TOCFL / Workshop')
                        ->schema([
                            TextInput::make('extra_info_title.id')
                                ->label('Judul Section Informasi Tambahan')
                                ->placeholder('cth. Available Levels / Testing Information / Workshop Highlights')
                                ->maxLength(100),
                            Repeater::make('extra_info_items')
                                ->label('Item Informasi Tambahan (Cards)')
                                ->schema([
                                    TextInput::make('label')
                                        ->label('Label')
                                        ->placeholder('cth. Level 1 / Session 1 / Format')
                                        ->required()
                                        ->maxLength(60),
                                    TextInput::make('value')
                                        ->label('Keterangan / Nilai')
                                        ->placeholder('cth. Novice Low / Computer-Based')
                                        ->required()
                                        ->maxLength(100),
                                    Select::make('color')
                                        ->label('Warna Card')
                                        ->options([
                                            'rose' => 'Rose (Pink / Merah Muda)',
                                            'amber' => 'Amber (Kuning / Jingga)',
                                            'blue' => 'Blue (Biru)',
                                            'emerald' => 'Emerald (Hijau)',
                                            'slate' => 'Slate (Abu-abu)',
                                        ])
                                        ->default('emerald'),
                                ])
                                ->columns(3)
                                ->defaultItems(0)
                                ->collapsible(),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Klasifikasi & Status Program')
                        ->schema([
                            Select::make('category')
                                ->label('Kategori Program')
                                ->options([
                                    MandarinProgram::CATEGORY_MANDARIN_CLASS => 'Mandarin Class',
                                    MandarinProgram::CATEGORY_TOCFL => 'TOCFL',
                                    MandarinProgram::CATEGORY_WORKSHOP_EVENT => 'Workshop / Event',
                                ])
                                ->required()
                                ->default(MandarinProgram::CATEGORY_MANDARIN_CLASS),

                            Select::make('status_override')
                                ->label('Status Program')
                                ->options([
                                    MandarinProgram::STATUS_AUTO => 'Otomatis (Berdasarkan Tanggal)',
                                    MandarinProgram::STATUS_UPCOMING => 'Upcoming',
                                    MandarinProgram::STATUS_REGISTRATION_OPEN => 'Registration Open',
                                    MandarinProgram::STATUS_REGISTRATION_CLOSED => 'Registration Closed',
                                    MandarinProgram::STATUS_PROGRAM_DONE => 'Program Done',
                                ])
                                ->default(MandarinProgram::STATUS_AUTO)
                                ->helperText('Gunakan "Otomatis" agar sistem menghitung status berdasarkan tanggal, atau pilih opsi override.'),

                            Toggle::make('show_details')
                                ->label('Tampilkan Tombol "View Details"')
                                ->default(true)
                                ->helperText('Mengizinkan pengunjung membuka popup detail program.'),

                            Toggle::make('is_active')
                                ->label('Aktif / Publikasikan')
                                ->default(true),
                        ]),

                    Section::make('Lokasi & Narahubung')
                        ->schema([
                            TextInput::make('location.id')
                                ->label('Lokasi')
                                ->default('Jakarta Taipei School')
                                ->maxLength(100),
                            Textarea::make('contact_info.id')
                                ->label('Kontak / Narahubung')
                                ->placeholder('cth. Huayu Language Center | huayu@jts.sch.id')
                                ->rows(2)
                                ->maxLength(255),
                        ]),

                    Section::make('Media Visual')
                        ->schema([
                            FileUpload::make('cover_image')
                                ->label('Cover / Brosur Program')
                                ->disk('public')
                                ->directory('mandarin-programs')
                                ->image()
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(4096)
                                ->imageEditor()
                                ->helperText('Format: JPG/PNG/WebP, rasio poster portrait disarankan.'),

                            FileUpload::make('gallery_images')
                                ->label('Galeri Foto Tambahan (Popup)')
                                ->disk('public')
                                ->directory('mandarin-programs')
                                ->image()
                                ->multiple()
                                ->reorderable()
                                ->appendFiles()
                                ->maxFiles(3)
                                ->maxSize(4096)
                                ->panelLayout('grid')
                                ->helperText('Maksimal 3 foto dokumentasi tambahan yang dapat dilihat di popup modal.'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Brosur')
                    ->state(fn (MandarinProgram $record): ?string => $record->cover_image_url ?: null)
                    ->defaultImageUrl(asset('images/logo.webp'))
                    ->square(),

                TextColumn::make('title')
                    ->label('Judul Program')
                    ->state(fn (MandarinProgram $record): string => $record->trans('title'))
                    ->weight('semibold')
                    ->searchable(query: function ($query, string $search) {
                        return $query->where('title', 'ilike', '%'.$search.'%');
                    })
                    ->description(fn (MandarinProgram $record): string => $record->trans('short_description')),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        MandarinProgram::CATEGORY_MANDARIN_CLASS => 'danger',
                        MandarinProgram::CATEGORY_TOCFL => 'info',
                        MandarinProgram::CATEGORY_WORKSHOP_EVENT => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (MandarinProgram $record): string => $record->getCategoryLabel()),

                TextColumn::make('computed_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        MandarinProgram::STATUS_REGISTRATION_OPEN => 'success',
                        MandarinProgram::STATUS_UPCOMING => 'info',
                        MandarinProgram::STATUS_REGISTRATION_CLOSED => 'warning',
                        MandarinProgram::STATUS_PROGRAM_DONE => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (MandarinProgram $record): string => $record->getStatusLabel()),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        MandarinProgram::CATEGORY_MANDARIN_CLASS => 'Mandarin Class',
                        MandarinProgram::CATEGORY_TOCFL => 'TOCFL',
                        MandarinProgram::CATEGORY_WORKSHOP_EVENT => 'Workshop / Event',
                    ]),
                SelectFilter::make('status_override')
                    ->label('Override Status')
                    ->options([
                        MandarinProgram::STATUS_AUTO => 'Otomatis',
                        MandarinProgram::STATUS_UPCOMING => 'Upcoming',
                        MandarinProgram::STATUS_REGISTRATION_OPEN => 'Registration Open',
                        MandarinProgram::STATUS_REGISTRATION_CLOSED => 'Registration Closed',
                        MandarinProgram::STATUS_PROGRAM_DONE => 'Program Done',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMandarinPrograms::route('/'),
            'create' => CreateMandarinProgram::route('/create'),
            'edit' => EditMandarinProgram::route('/{record}/edit'),
        ];
    }
}
