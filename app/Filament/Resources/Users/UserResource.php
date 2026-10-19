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
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                    Section::make(__('admin.sections.account_information'))
                        ->description(__('admin.descriptions.account_information'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('admin.labels.full_name'))
                                ->required()
                                ->validationMessages([
                                    'required' => __('admin.users.validation.required'),
                                    'max' => __('admin.users.validation.max'),
                                ])
                                ->maxLength(150),
                            TextInput::make('email')
                                ->label(__('admin.labels.email_address'))
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->validationMessages([
                                    'required' => __('admin.users.validation.required'),
                                    'email' => __('admin.users.validation.email'),
                                    'unique' => __('admin.users.validation.unique_email'),
                                    'max' => __('admin.users.validation.max'),
                                ])
                                ->maxLength(191),
                            TextInput::make('password')
                                ->label(__('admin.labels.password'))
                                ->password()
                                ->revealable()
                                ->autocomplete('new-password')
                                ->afterStateHydrated(fn (TextInput $component) => $component->state(null))
                                ->minLength(12)
                                ->maxLength(72)
                                ->required(fn (string $operation) => $operation === 'create')
                                ->same('password_confirmation')
                                ->validationMessages([
                                    'required' => __('admin.users.validation.required'),
                                    'min' => __('admin.users.validation.min'),
                                    'max' => __('admin.users.validation.max'),
                                    'same' => __('admin.users.validation.password_mismatch'),
                                ])
                                ->dehydrated(fn (?string $state): bool => filled($state))
                                ->helperText(__('admin.helpers.password_edit')),
                            TextInput::make('password_confirmation')
                                ->label(__('admin.users.password_confirmation'))
                                ->password()
                                ->revealable()
                                ->autocomplete('new-password')
                                ->requiredWith('password')
                                ->validationMessages([
                                    'required_with' => __('admin.users.validation.confirmation_required'),
                                ])
                                ->dehydrated(false),
                        ])
                        ->columns(2),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make(__('admin.sections.role_permissions'))
                        ->description(__('admin.descriptions.role_permissions'))
                        ->schema([
                            Select::make('role_id')
                                ->label(__('admin.labels.role'))
                                ->relationship('role', 'name', modifyQueryUsing: fn (Builder $query) => $query->whereIn('code', [
                                    'super_admin',
                                    'admissions',
                                    'academic',
                                    'student_affairs',
                                    'clc',
                                    'kindergarten',
                                    'secretary',
                                    'languages',
                                    'library',
                                ]))
                                ->getOptionLabelFromRecordUsing(fn (Role $record) => __('admin.users.roles.'.$record->code) ?? $record->name)
                                ->default(fn () => Role::where('code', 'super_admin')->value('id'))
                                ->required()
                                ->native(true),
                            Toggle::make('is_active')
                                ->label(__('admin.fields.active_status'))
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
                    ->label(__('admin.labels.user_name'))
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('email')
                    ->label(__('admin.fields.email'))
                    ->searchable(),
                TextColumn::make('role.code')
                    ->label(__('admin.labels.role'))
                    ->formatStateUsing(fn (?string $state): string => $state ? (__('admin.users.roles.'.$state) ?? $state) : '-')
                    ->badge()
                    ->color('primary'),
                IconColumn::make('is_active')
                    ->label(__('admin.fields.active_status'))
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
