<?php

namespace App\Filament\Resources\StaffProfiles;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\StaffProfile;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
                    Section::make(__('admin.sections.personal_position'))
                        ->description(__('admin.descriptions.personal_position'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('admin.labels.full_name'))
                                ->required()
                                ->maxLength(150),
                            TextInput::make('title')
                                ->label(__('admin.labels.position'))
                                ->required()
                                ->maxLength(150),
                            CmsFields::department(),
                        ])
                        ->columns(2),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make(__('admin.sections.profile_photo'))
                        ->schema([
                            CmsFields::image('photo', 2048, __('admin.sections.profile_photo')),
                        ]),

                    Section::make(__('admin.sections.status_order'))
                        ->schema([
                            TextInput::make('sort_order')
                                ->label(__('admin.fields.display_order'))
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                            Toggle::make('is_active')
                                ->label(__('admin.labels.active_show'))
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
                    ->label(__('admin.labels.staff_name'))
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('title')
                    ->label(__('admin.labels.position')),
                TextColumn::make('department.name')
                    ->label(__('admin.fields.department')),
                IconColumn::make('is_active')
                    ->label(__('admin.fields.active'))
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
