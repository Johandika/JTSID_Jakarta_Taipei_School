<?php

namespace App\Filament\Resources\Departments;

use App\Filament\Resources\CmsResource;
use App\Models\Department;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentResource extends CmsResource
{
    protected static ?string $model = Department::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Departments';

    protected static ?string $pluralModelLabel = 'Departments';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('admin.sections.department_information'))
                    ->description(__('admin.descriptions.department_information'))
                    ->schema([
                        TextInput::make('code')
                            ->label(__('admin.labels.department_code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label(__('admin.labels.department_name'))
                            ->required()
                            ->maxLength(150),
                        TextInput::make('sort_order')
                            ->label(__('admin.fields.display_order'))
                            ->integer()
                            ->minValue(0)
                            ->required()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label(__('admin.fields.active_status'))
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.labels.department_name'))
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('code')
                    ->label(__('admin.fields.code'))
                    ->badge()
                    ->color('gray'),
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
