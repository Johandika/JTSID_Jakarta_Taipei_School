<?php

namespace App\Filament\Resources\MandarinPrograms;

use App\Filament\Resources\CmsResource;
use App\Filament\Resources\MandarinPrograms\Pages\CreateMandarinProgram;
use App\Filament\Resources\MandarinPrograms\Pages\EditMandarinProgram;
use App\Filament\Resources\MandarinPrograms\Pages\ListMandarinPrograms;
use App\Models\MandarinProgram;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MandarinProgramResource extends CmsResource
{
    protected static ?string $model = MandarinProgram::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'School';

    protected static ?string $navigationLabel = 'Short-Term Mandarin Programs';

    protected static ?string $pluralModelLabel = 'Short-Term Mandarin Programs';

    protected static ?string $modelLabel = 'Mandarin Program';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make(__('admin.sections.program_main_information'))
                        ->description(__('admin.descriptions.program_main_information'))
                        ->schema([
                            Tabs::make('ProgramTranslations')
                                ->tabs([
                                    Tab::make('id')
                                        ->label('🇮🇩 Indonesian')
                                        ->schema([
                                            TextInput::make('title.id')
                                                ->label(__('admin.labels.program_title_id'))
                                                ->placeholder(__('admin.placeholders.program_title'))
                                                ->required()
                                                ->maxLength(191),
                                            Textarea::make('short_description.id')
                                                ->label(__('admin.labels.short_description_id'))
                                                ->placeholder(__('admin.placeholders.program_short_description'))
                                                ->rows(2)
                                                ->maxLength(300),
                                            Textarea::make('description.id')
                                                ->label(__('admin.labels.program_description_id'))
                                                ->placeholder(__('admin.placeholders.program_description'))
                                                ->rows(4),
                                        ]),

                                    Tab::make('en')
                                        ->label('🇬🇧 English')
                                        ->schema([
                                            TextInput::make('title.en')
                                                ->label(__('admin.labels.program_title_en'))
                                                ->placeholder('e.g. Mandarin Classes')
                                                ->maxLength(191),
                                            Textarea::make('short_description.en')
                                                ->label('Short Description (EN)')
                                                ->placeholder('Short summary displayed on program card...')
                                                ->rows(2)
                                                ->maxLength(300),
                                            Textarea::make('description.en')
                                                ->label(__('admin.labels.program_description_en'))
                                                ->placeholder('Detailed information about the program...')
                                                ->rows(4),
                                        ]),

                                    Tab::make('zh-TW')
                                        ->label('🇹🇼 Traditional Chinese')
                                        ->schema([
                                            TextInput::make('title.zh-TW')
                                                ->label('課程名稱 (繁中)')
                                                ->placeholder('例如：華語班 / 華語文能力測驗')
                                                ->maxLength(191),
                                            Textarea::make('short_description.zh-TW')
                                                ->label('簡要說明 (繁中)')
                                                ->placeholder('卡片簡述...')
                                                ->rows(2)
                                                ->maxLength(300),
                                            Textarea::make('description.zh-TW')
                                                ->label('課程詳情介紹 (繁中)')
                                                ->placeholder('完整課程規劃與內容說明...')
                                                ->rows(4),
                                        ]),
                                ]),
                        ]),

                    Section::make(__('admin.sections.program_schedule'))
                        ->description(__('admin.descriptions.program_schedule'))
                        ->schema([
                            TextInput::make('program_date_label.id')
                                ->label(__('admin.labels.date_label_id'))
                                ->placeholder(__('admin.placeholders.program_date_label'))
                                ->maxLength(60),
                            TextInput::make('program_date_text.id')
                                ->label(__('admin.labels.custom_date_text_id'))
                                ->placeholder(__('admin.placeholders.program_date_text'))
                                ->helperText(__('admin.helpers.custom_date_text'))
                                ->maxLength(100),
                            DatePicker::make('program_start_date')
                                ->label(__('admin.labels.program_start_date'))
                                ->native(false),
                            DatePicker::make('program_end_date')
                                ->label(__('admin.labels.program_end_date'))
                                ->native(false),
                        ])
                        ->columns(2),

                    Section::make(__('admin.sections.registration_period_link'))
                        ->description(__('admin.descriptions.registration_period_link'))
                        ->schema([
                            TextInput::make('registration_date_text.id')
                                ->label(__('admin.labels.custom_registration_period'))
                                ->placeholder(__('admin.placeholders.registration_period'))
                                ->columnSpanFull()
                                ->maxLength(100),
                            DatePicker::make('registration_start_date')
                                ->label(__('admin.labels.registration_start_date'))
                                ->native(false),
                            DatePicker::make('registration_end_date')
                                ->label(__('admin.labels.registration_end_date'))
                                ->native(false),
                            TextInput::make('registration_url')
                                ->label(__('admin.labels.registration_url'))
                                ->placeholder(__('admin.placeholders.registration_url'))
                                ->url()
                                ->columnSpanFull()
                                ->helperText(__('admin.helpers.registration_url')),
                        ])
                        ->columns(2),

                    Section::make(__('admin.sections.extra_information'))
                        ->description(__('admin.descriptions.extra_information'))
                        ->schema([
                            TextInput::make('extra_info_title.id')
                                ->label(__('admin.labels.extra_info_title'))
                                ->placeholder(__('admin.placeholders.extra_info_title'))
                                ->maxLength(100),
                            Repeater::make('extra_info_items')
                                ->label(__('admin.labels.extra_info_items'))
                                ->schema([
                                    TextInput::make('label')
                                        ->label('Label')
                                        ->placeholder(__('admin.placeholders.extra_info_label'))
                                        ->required()
                                        ->maxLength(60),
                                    TextInput::make('value')
                                        ->label(__('admin.labels.description_value'))
                                        ->placeholder(__('admin.placeholders.extra_info_value'))
                                        ->required()
                                        ->maxLength(100),
                                    Select::make('color')
                                        ->label(__('admin.labels.card_color'))
                                        ->options([
                                            'rose' => 'Rose (Pink / Merah Muda)',
                                            'amber' => 'Amber (Kuning / Jingga)',
                                            'blue' => 'Blue (Biru)',
                                            'emerald' => 'Emerald (Hijau)',
                                            'slate' => 'Slate (Abu-abu)',
                                        ])
                                        ->default('emerald'),
                                ])
                                ->columns(3)
                                ->defaultItems(0)
                                ->collapsible(),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make(__('admin.sections.program_classification_status'))
                        ->schema([
                            Select::make('category')
                                ->label(__('admin.labels.program_category'))
                                ->options([
                                    MandarinProgram::CATEGORY_MANDARIN_CLASS => 'Mandarin Class',
                                    MandarinProgram::CATEGORY_TOCFL => 'TOCFL',
                                    MandarinProgram::CATEGORY_WORKSHOP_EVENT => 'Workshop / Event',
                                ])
                                ->required()
                                ->default(MandarinProgram::CATEGORY_MANDARIN_CLASS),

                            Select::make('status_override')
                                ->label(__('admin.labels.program_status'))
                                ->options([
                                    MandarinProgram::STATUS_AUTO => __('admin.mandarin_status.auto'),
                                    MandarinProgram::STATUS_UPCOMING => 'Upcoming',
                                    MandarinProgram::STATUS_REGISTRATION_OPEN => 'Registration Open',
                                    MandarinProgram::STATUS_REGISTRATION_CLOSED => 'Registration Closed',
                                    MandarinProgram::STATUS_PROGRAM_DONE => __('admin.mandarin_status.program_done'),
                                ])
                                ->default(MandarinProgram::STATUS_AUTO)
                                ->helperText(__('admin.helpers.status_override')),

                            Toggle::make('show_details')
                                ->label(__('admin.labels.show_view_details'))
                                ->default(true)
                                ->helperText(__('admin.helpers.show_view_details')),

                            Toggle::make('is_active')
                                ->label(__('admin.labels.active_publish'))
                                ->default(true),
                        ]),

                    Section::make(__('admin.sections.location_contact'))
                        ->schema([
                            TextInput::make('location.id')
                                ->label(__('admin.labels.location'))
                                ->default('Jakarta Taipei School')
                                ->maxLength(100),
                            Textarea::make('contact_info.id')
                                ->label(__('admin.labels.contact_person'))
                                ->placeholder(__('admin.placeholders.contact_person'))
                                ->rows(2)
                                ->maxLength(255),
                        ]),

                    Section::make(__('admin.sections.visual_media'))
                        ->schema([
                            FileUpload::make('cover_image')
                                ->label(__('admin.labels.program_brochure_cover'))
                                ->disk('public')
                                ->directory('mandarin-programs')
                                ->image()
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(4096)
                                ->imageEditor()
                                ->helperText(__('admin.helpers.program_brochure_cover')),

                            FileUpload::make('gallery_images')
                                ->label(__('admin.labels.gallery_images'))
                                ->disk('public')
                                ->directory('mandarin-programs')
                                ->image()
                                ->multiple()
                                ->reorderable()
                                ->appendFiles()
                                ->maxFiles(3)
                                ->maxSize(4096)
                                ->panelLayout('grid')
                                ->helperText(__('admin.helpers.gallery_images')),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label(__('admin.labels.brochure'))
                    ->state(fn (MandarinProgram $record): ?string => $record->cover_image_url ?: null)
                    ->defaultImageUrl(asset('images/logo.webp'))
                    ->square(),

                TextColumn::make('title')
                    ->label(__('admin.labels.program_title'))
                    ->state(fn (MandarinProgram $record): string => $record->trans('title'))
                    ->weight('semibold')
                    ->searchable(query: function ($query, string $search) {
                        return $query->where('title', 'ilike', '%'.$search.'%');
                    })
                    ->description(fn (MandarinProgram $record): string => $record->trans('short_description')),

                TextColumn::make('category')
                    ->label(__('admin.fields.category'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        MandarinProgram::CATEGORY_MANDARIN_CLASS => 'danger',
                        MandarinProgram::CATEGORY_TOCFL => 'info',
                        MandarinProgram::CATEGORY_WORKSHOP_EVENT => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (MandarinProgram $record): string => $record->getCategoryLabel()),

                TextColumn::make('computed_status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        MandarinProgram::STATUS_REGISTRATION_OPEN => 'success',
                        MandarinProgram::STATUS_UPCOMING => 'info',
                        MandarinProgram::STATUS_REGISTRATION_CLOSED => 'warning',
                        MandarinProgram::STATUS_PROGRAM_DONE => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (MandarinProgram $record): string => $record->getStatusLabel()),

                IconColumn::make('is_active')
                    ->label(__('admin.fields.active'))
                    ->boolean(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->label(__('admin.fields.category'))
                    ->options([
                        MandarinProgram::CATEGORY_MANDARIN_CLASS => 'Mandarin Class',
                        MandarinProgram::CATEGORY_TOCFL => 'TOCFL',
                        MandarinProgram::CATEGORY_WORKSHOP_EVENT => 'Workshop / Event',
                    ]),
                SelectFilter::make('status_override')
                    ->label(__('admin.labels.status_override'))
                    ->options([
                        MandarinProgram::STATUS_AUTO => __('admin.mandarin_status.auto_short'),
                        MandarinProgram::STATUS_UPCOMING => 'Upcoming',
                        MandarinProgram::STATUS_REGISTRATION_OPEN => 'Registration Open',
                        MandarinProgram::STATUS_REGISTRATION_CLOSED => 'Registration Closed',
                        MandarinProgram::STATUS_PROGRAM_DONE => __('admin.mandarin_status.program_done'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMandarinPrograms::route('/'),
            'create' => CreateMandarinProgram::route('/create'),
            'edit' => EditMandarinProgram::route('/{record}/edit'),
        ];
    }
}
