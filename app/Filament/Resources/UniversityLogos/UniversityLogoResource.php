<?php

namespace App\Filament\Resources\UniversityLogos;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\UniversityLogo;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UniversityLogoResource extends CmsResource
{
    protected static ?string $model = UniversityLogo::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'University Logos';

    protected static ?string $pluralModelLabel = 'University Logos';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Logo Universitas Lulusan')
                        ->description('Unggah logo kampus tempat siswa lulusan JTS diterima/melanjutkan studi')
                        ->schema([
                            CmsFields::image('logo', 2048, 'File Logo Universitas')
                                ->label('Logo Universitas')
                                ->helperText('Disarankan format PNG transparan, WebP, atau SVG dengan resolusi tajam (Maks. 2MB)')
                                ->required(),
                            TextInput::make('title')
                                ->label('Nama / Label Universitas (Opsional)')
                                ->placeholder('Contoh: Harvard University, Oxford, NTU')
                                ->maxLength(191),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Status & Urutan')
                        ->schema([
                            TextInput::make('sort_order')
                                ->label('Urutan Tampilan')
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                            Toggle::make('is_active')
                                ->label('Aktif / Tampilkan di Marquee')
                                ->default(true),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('logo')
                ->label('Logo')
                ->disk('public'),
            TextColumn::make('title')
                ->label('Nama / Keterangan')
                ->searchable()
                ->placeholder('—')
                ->weight('medium'),
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
            'index' => Pages\ListUniversityLogos::route('/'),
            'create' => Pages\CreateUniversityLogo::route('/create'),
            'edit' => Pages\EditUniversityLogo::route('/{record}/edit'),
        ];
    }
}
