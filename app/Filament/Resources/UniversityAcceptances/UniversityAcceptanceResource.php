<?php

namespace App\Filament\Resources\UniversityAcceptances;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\UniversityAcceptance;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UniversityAcceptanceResource extends CmsResource
{
    protected static ?string $model = UniversityAcceptance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $navigationLabel = 'University Acceptances';

    protected static ?string $pluralModelLabel = 'University Acceptances';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Data Siswa & Universitas')
                        ->description('Informasi siswa, universitas tujuan, dan program studi yang diterima')
                        ->schema([
                            TextInput::make('student_name')
                                ->label('Nama Siswa')
                                ->required()
                                ->maxLength(150),
                            TextInput::make('university_name')
                                ->label('Nama Universitas')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('program_name')
                                ->label('Program Studi / Jurusan')
                                ->maxLength(191),
                            TextInput::make('admission_path')
                                ->label('Jalur Penerimaan')
                                ->placeholder('Contoh: SNMPTN, International Admission, Beasiswa')
                                ->maxLength(150),
                            TextInput::make('country')
                                ->label('Negara Universitas')
                                ->maxLength(100),
                            TextInput::make('image_url')
                                ->label('Link Foto / Gambar')
                                ->placeholder('https://drive.google.com/file/d/... atau https://...')
                                ->url()
                                ->maxLength(2048)
                                ->helperText('Opsional. Untuk bulk import, isi kolom image_url dengan link gambar yang bisa diakses publik.'),
                        ])
                        ->columns(2),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Tahun Ajaran & Departemen')
                        ->schema([
                            Select::make('academic_year_id')
                                ->label('Tahun Ajaran')
                                ->relationship('academicYear', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            CmsFields::department(),
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
                                ->label('Aktif / Tampilkan')
                                ->default(true),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Gambar')
                    ->state(fn (UniversityAcceptance $record): ?string => $record->publicImageUrl())
                    ->placeholder('-')
                    ->square()
                    ->size(56),
                TextColumn::make('student_name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('university_name')
                    ->label('Universitas')
                    ->searchable(),
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran'),
                TextColumn::make('program_name')
                    ->label('Program Studi'),
                TextColumn::make('country')
                    ->label('Negara')
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('Aktif')
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
            'index' => Pages\ListUniversityAcceptances::route('/'),
            'create' => Pages\CreateUniversityAcceptance::route('/create'),
            'edit' => Pages\EditUniversityAcceptance::route('/{record}/edit'),
        ];
    }
}
