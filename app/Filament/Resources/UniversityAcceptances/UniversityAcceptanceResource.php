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
                    Section::make(__('admin.sections.student_university_data'))
                        ->description(__('admin.descriptions.student_university_data'))
                        ->schema([
                            TextInput::make('student_name')
                                ->label(__('admin.labels.student_name'))
                                ->required()
                                ->maxLength(150),
                            TextInput::make('university_name')
                                ->label(__('admin.labels.university_name'))
                                ->required()
                                ->maxLength(191),
                            TextInput::make('program_name')
                                ->label(__('admin.labels.study_program'))
                                ->maxLength(191),
                            TextInput::make('admission_path')
                                ->label(__('admin.labels.admission_path'))
                                ->placeholder(__('admin.placeholders.admission_path'))
                                ->maxLength(150),
                            TextInput::make('country')
                                ->label(__('admin.labels.university_country'))
                                ->maxLength(100),
                            TextInput::make('image_url')
                                ->label(__('admin.labels.image_url'))
                                ->placeholder(__('admin.placeholders.image_url'))
                                ->url()
                                ->maxLength(2048)
                                ->helperText(__('admin.helpers.image_url')),
                        ])
                        ->columns(2),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make(__('admin.sections.academic_year_department'))
                        ->schema([
                            Select::make('academic_year_id')
                                ->label(__('admin.fields.academic_year'))
                                ->relationship('academicYear', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            CmsFields::department(),
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
                ImageColumn::make('image_url')
                    ->label(__('admin.fields.image'))
                    ->state(fn (UniversityAcceptance $record): ?string => $record->publicImageUrl())
                    ->placeholder('-')
                    ->square()
                    ->size(56),
                TextColumn::make('student_name')
                    ->label(__('admin.labels.student_name'))
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('university_name')
                    ->label(__('admin.labels.university'))
                    ->searchable(),
                TextColumn::make('academicYear.name')
                    ->label(__('admin.fields.academic_year')),
                TextColumn::make('program_name')
                    ->label(__('admin.labels.study_program')),
                TextColumn::make('country')
                    ->label(__('admin.labels.country'))
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label(__('admin.fields.active'))
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
