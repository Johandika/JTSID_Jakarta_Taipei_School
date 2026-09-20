<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\CmsResource;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends CmsResource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Akun Pengguna')
                        ->description('Identitas akun dan kata sandi akses sistem')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(150),
                            TextInput::make('email')
                                ->label('Alamat Email')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(191),
                            TextInput::make('password')
                                ->label('Kata Sandi')
                                ->password()
                                ->revealable()
                                ->minLength(12)
                                ->required(fn (string $operation) => $operation === 'create')
                                ->dehydrated(fn ($state) => filled($state))
                                ->helperText('Minimal 12 karakter. Kosongkan saat edit jika tidak ingin mengubah sandi.')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Peran & Hak Akses')
                        ->description('Penetapan izin dan departemen')
                        ->schema([
                            Select::make('role_id')
                                ->label('Peran / Role')
                                ->relationship('role', 'name')
                                ->required()
                                ->live()
                                ->disabled(fn (?Model $record) => $record?->id === auth()->id()),
                            Select::make('department_id')
                                ->label('Departemen Pengelola')
                                ->relationship('department', 'name')
                                ->required(fn (Get $get) => Role::find($get('role_id'))?->code === 'department_admin'),
                            Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true)
                                ->disabled(fn (?Model $record) => $record?->id === auth()->id()),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('role.name')
                    ->label('Peran')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('department.name')
                    ->label('Departemen')
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label('Status Aktif')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
