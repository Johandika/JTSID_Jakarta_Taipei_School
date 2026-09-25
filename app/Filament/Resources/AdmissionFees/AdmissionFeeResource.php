<?php

namespace App\Filament\Resources\AdmissionFees;

use App\Filament\Resources\AdmissionFees\Pages\CreateAdmissionFee;
use App\Filament\Resources\AdmissionFees\Pages\EditAdmissionFee;
use App\Filament\Resources\AdmissionFees\Pages\ListAdmissionFees;
use App\Filament\Resources\CmsResource;
use App\Models\AdmissionFee;
use App\Models\GradeLevel;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdmissionFeeResource extends CmsResource
{
    protected static ?string $model = AdmissionFee::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Admissions';

    protected static ?string $navigationLabel = 'Rincian Biaya';

    protected static ?string $pluralModelLabel = 'Rincian Biaya';

    protected static ?string $modelLabel = 'Item Rincian Biaya';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Biaya & Jenjang')
                        ->description('Pilih jenjang sekolah dan tentukan rincian biaya')
                        ->schema([
                            Select::make('grade_level_id')
                                ->label('Jenjang Pendidikan')
                                ->options(fn () => GradeLevel::pluck('name', 'id'))
                                ->default(function ($livewire) {
                                    $code = $livewire->grade_level ?? request()->query('grade_level');

                                    return $code ? GradeLevel::where('code', $code)->value('id') : null;
                                })
                                ->required()
                                ->native(false),

                            TextInput::make('amount')
                                ->label('Nominal / Besaran Biaya')
                                ->placeholder('cth. Rp. 1.000.000,- atau ± Rp. 5–6 juta')
                                ->required()
                                ->maxLength(100),

                            Tabs::make('FeeTranslations')
                                ->tabs([
                                    Tab::make('id')
                                        ->label('🇮🇩 Indonesian (Bahasa Indonesia)')
                                        ->schema([
                                            TextInput::make('name.id')
                                                ->label('Nama Jenis Biaya (ID)')
                                                ->placeholder('cth. Uang Pendaftaran')
                                                ->required()
                                                ->maxLength(150),
                                            TextInput::make('payment_period.id')
                                                ->label('Periode Pembayaran (ID)')
                                                ->placeholder('cth. satu kali pembayaran')
                                                ->required()
                                                ->maxLength(150),
                                            Textarea::make('note.id')
                                                ->label('Catatan Tambahan Opsional (ID)')
                                                ->placeholder("cth. Untuk anak kedua Rp. 3.500.000,-\nAnak ketiga gratis")
                                                ->rows(3),
                                        ]),

                                    Tab::make('en')
                                        ->label('🇬🇧 English')
                                        ->schema([
                                            TextInput::make('name.en')
                                                ->label('Fee Name (EN)')
                                                ->placeholder('e.g. Registration Fee')
                                                ->maxLength(150),
                                            TextInput::make('payment_period.en')
                                                ->label('Payment Period (EN)')
                                                ->placeholder('e.g. one-time payment')
                                                ->maxLength(150),
                                            Textarea::make('note.en')
                                                ->label('Optional Note (EN)')
                                                ->placeholder("e.g. For second child Rp. 3,500,000,-\nThird child free")
                                                ->rows(3),
                                        ]),

                                    Tab::make('zh-TW')
                                        ->label('🇹🇼 Mandarin (繁體中文)')
                                        ->schema([
                                            TextInput::make('name.zh-TW')
                                                ->label('費用項目名稱 (ZH)')
                                                ->placeholder('例：報名費')
                                                ->maxLength(150),
                                            TextInput::make('payment_period.zh-TW')
                                                ->label('繳納週期 (ZH)')
                                                ->placeholder('例：一次性繳納')
                                                ->maxLength(150),
                                            Textarea::make('note.zh-TW')
                                                ->label('額外備註 (ZH)')
                                                ->placeholder("例：第二位子女享優惠...\n第三位子女免費")
                                                ->rows(3),
                                        ]),
                                ]),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make()->schema([
                    Section::make('Visual & Tampilan')
                        ->description('Kustomisasi icon dan warna latar belakang kotak icon')
                        ->schema([
                            Select::make('icon')
                                ->label('Pilihan Icon')
                                ->options([
                                    'file-text' => '📄 Dokumen / Formulir (file-text)',
                                    'building' => '🏛️ Gedung / Pembangunan (building)',
                                    'backpack' => '🎒 Tas Sekolah / SPP (backpack)',
                                    'book-open' => '📖 Buku & Kegiatan (book-open)',
                                    'graduation-cap' => '🎓 Akademik / Kelulusan (graduation-cap)',
                                    'award' => '🏆 Beasiswa / Prestasi (award)',
                                    'credit-card' => '💳 Pembayaran Umum (credit-card)',
                                ])
                                ->default('file-text')
                                ->required()
                                ->native(false),

                            Select::make('icon_color')
                                ->label('Warna Latar Kotak Icon')
                                ->options([
                                    'green' => '🟢 Hijau (Emerald/Green)',
                                    'blue' => '🔵 Biru (Sky/Blue)',
                                    'pink' => '🔴 Merah Muda (Rose/Pink)',
                                    'amber' => '🟡 Kuning (Amber/Orange)',
                                ])
                                ->default('green')
                                ->required()
                                ->native(false),
                        ]),

                    Section::make('Status & Urutan')
                        ->schema([
                            TextInput::make('sort_order')
                                ->label('Nomor Urut')
                                ->numeric()
                                ->default(0)
                                ->helperText('Urutan tampilan dalam tabel'),

                            Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true)
                                ->helperText('Jika non-aktif, item biaya tidak akan ditampilkan di halaman publik'),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gradeLevel.name')
                    ->label('Jenjang')
                    ->badge()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Jenis Biaya')
                    ->getStateUsing(fn (AdmissionFee $record): string => $record->trans('name'))
                    ->weight('bold')
                    ->searchable(query: fn ($query, $search) => $query->where('name', 'ilike', '%'.$search.'%')),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('payment_period')
                    ->label('Periode')
                    ->getStateUsing(fn (AdmissionFee $record): string => $record->trans('payment_period')),

                TextColumn::make('icon_color')
                    ->label('Tema Warna')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'green' => 'success',
                        'blue' => 'info',
                        'pink' => 'danger',
                        'amber' => 'warning',
                        default => 'gray',
                    }),

                IconColumn::make('has_note')
                    ->label('Ada Catatan')
                    ->boolean()
                    ->getStateUsing(fn (AdmissionFee $record): bool => ! empty(trim($record->trans('note')))),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 'all'])
            ->filters([
                SelectFilter::make('grade_level_id')
                    ->label('Filter Jenjang')
                    ->options(fn () => GradeLevel::pluck('name', 'id')),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdmissionFees::route('/'),
            'create' => CreateAdmissionFee::route('/create'),
            'edit' => EditAdmissionFee::route('/{record}/edit'),
        ];
    }
}
