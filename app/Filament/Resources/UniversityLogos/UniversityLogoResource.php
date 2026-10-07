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
                    Section::make(__('admin.sections.university_logo'))
                        ->description(__('admin.descriptions.university_logo'))
                        ->schema([
                            CmsFields::image('logo', 2048, __('admin.labels.university_logo_file'))
                                ->label(__('admin.labels.university_logo'))
                                ->helperText(__('admin.helpers.university_logo'))
                                ->required(),
                            TextInput::make('title')
                                ->label(__('admin.labels.university_label_optional'))
                                ->placeholder(__('admin.placeholders.university_label'))
                                ->maxLength(191),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make(__('admin.sections.status_order'))
                        ->schema([
                            TextInput::make('sort_order')
                                ->label(__('admin.fields.display_order'))
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                            Toggle::make('is_active')
                                ->label(__('admin.labels.active_show_marquee'))
                                ->default(true),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('logo')
                ->label(__('admin.labels.logo'))
                ->disk('public'),
            TextColumn::make('title')
                ->label(__('admin.labels.name_description'))
                ->searchable()
                ->placeholder('—')
                ->weight('medium'),
            TextColumn::make('sort_order')
                ->label(__('admin.fields.sort_order'))
                ->sortable(),
            IconColumn::make('is_active')
                ->label(__('admin.fields.active'))
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
