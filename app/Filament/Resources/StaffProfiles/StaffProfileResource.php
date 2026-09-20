<?php

namespace App\Filament\Resources\StaffProfiles;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\StaffProfile;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StaffProfileResource extends CmsResource
{
    protected static ?string $model = StaffProfile::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $navigationLabel = 'Staff';

    protected static ?string $pluralModelLabel = 'Staff';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Pribadi & Posisi')
                        ->description('Identitas, jabatan, dan departemen staf pengajar')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(150),
                            TextInput::make('title')
                                ->label('Jabatan / Posisi')
                                ->required()
                                ->maxLength(150),
                            CmsFields::department(),
                            TextInput::make('extension')
                                ->label('Nomor Ekstensi Telp')
                                ->maxLength(50),
                        ])
                        ->columns(2),

                    Section::make('Tanggung Jawab & Peran')
                        ->description('Rincian tugas dan tanggung jawab di sekolah')
                        ->schema([
                            Textarea::make('responsibilities')
                                ->label('Rincian Tugas')
                                ->rows(4)
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Foto Profil')
                        ->schema([
                            CmsFields::image('photo', 2048, 'Foto Profil'),
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
                TextColumn::make('name')
                    ->label('Nama Staf')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('title')
                    ->label('Jabatan'),
                TextColumn::make('department.name')
                    ->label('Departemen'),
                TextColumn::make('extension')
                    ->label('Ekstensi'),
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
            'index' => Pages\ListStaffProfiles::route('/'),
            'create' => Pages\CreateStaffProfile::route('/create'),
            'edit' => Pages\EditStaffProfile::route('/{record}/edit'),
        ];
    }
}
