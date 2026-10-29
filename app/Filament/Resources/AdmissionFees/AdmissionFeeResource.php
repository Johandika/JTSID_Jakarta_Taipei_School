<?php

namespace App\Filament\Resources\AdmissionFees;

use App\Filament\Resources\AdmissionFees\Pages\CreateAdmissionFee;
use App\Filament\Resources\AdmissionFees\Pages\EditAdmissionFee;
use App\Filament\Resources\AdmissionFees\Pages\ListAdmissionFees;
use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\AdmissionFee;
use App\Models\GradeLevel;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdmissionFeeResource extends CmsResource
{
    protected static ?string $model = AdmissionFee::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Admissions';

    protected static ?string $navigationLabel = 'Rincian Biaya';

    protected static ?string $pluralModelLabel = 'Rincian Biaya';

    protected static ?string $modelLabel = 'Item Rincian Biaya';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make(__('admin.sections.fee_level_information'))
                        ->description(__('admin.descriptions.fee_level_information'))
                        ->schema([
                            Select::make('grade_level_id')
                                ->label(__('admin.labels.education_level'))
                                ->options(fn () => GradeLevel::pluck('name', 'id'))
                                ->default(function ($livewire) {
                                    $code = $livewire->grade_level ?? request()->query('grade_level');

                                    return $code ? GradeLevel::where('code', $code)->value('id') : null;
                                })
                                ->required()
                                ->native(false),

                            TextInput::make('amount')
                                ->label(__('admin.labels.fee_amount'))
                                ->placeholder(__('admin.placeholders.fee_amount'))
                                ->required()
                                ->maxLength(100),

                            Tabs::make('FeeTranslations')
                                ->tabs([
                                    Tab::make('id')
                                        ->label('🇮🇩 Indonesian (Bahasa Indonesia)')
                                        ->schema([
                                            TextInput::make('name.id')
                                                ->label(__('admin.labels.fee_name_id'))
                                                ->placeholder(__('admin.placeholders.fee_name'))
                                                ->required()
                                                ->maxLength(150),
                                            TextInput::make('payment_period.id')
                                                ->label(__('admin.labels.payment_period_id'))
                                                ->placeholder(__('admin.placeholders.payment_period'))
                                                ->required()
                                                ->maxLength(150),
                                            Textarea::make('note.id')
                                                ->label(__('admin.labels.optional_note_id'))
                                                ->placeholder(__('admin.placeholders.fee_note'))
                                                ->rows(3),
                                            TextInput::make('file_label.id')
                                                ->label(__('admin.labels.fee_file_label_id'))
                                                ->placeholder(__('admin.placeholders.fee_file_label'))
                                                ->maxLength(100),
                                        ]),

                                    Tab::make('en')
                                        ->label('🇬🇧 English')
                                        ->schema([
                                            TextInput::make('name.en')
                                                ->label('Fee Name (EN)')
                                                ->placeholder('e.g. Registration Fee')
                                                ->maxLength(150),
                                            TextInput::make('payment_period.en')
                                                ->label('Payment Period (EN)')
                                                ->placeholder('e.g. one-time payment')
                                                ->maxLength(150),
                                            Textarea::make('note.en')
                                                ->label('Optional Note (EN)')
                                                ->placeholder("e.g. For second child Rp. 3,500,000,-\nThird child free")
                                                ->rows(3),
                                            TextInput::make('file_label.en')
                                                ->label('Download Button Text (EN)')
                                                ->placeholder('e.g. Discount terms')
                                                ->maxLength(100),
                                        ]),

                                    Tab::make('zh-TW')
                                        ->label('🇹🇼 Mandarin (繁體中文)')
                                        ->schema([
                                            TextInput::make('name.zh-TW')
                                                ->label('費用項目名稱 (ZH)')
                                                ->placeholder('例：報名費')
                                                ->maxLength(150),
                                            TextInput::make('payment_period.zh-TW')
                                                ->label('繳納週期 (ZH)')
                                                ->placeholder('例：一次性繳納')
                                                ->maxLength(150),
                                            Textarea::make('note.zh-TW')
                                                ->label('額外備註 (ZH)')
                                                ->placeholder("例：第二位子女享優惠...\n第三位子女免費")
                                                ->rows(3),
                                            TextInput::make('file_label.zh-TW')
                                                ->label('下載按鈕文字 (ZH)')
                                                ->placeholder('例：優惠說明')
                                                ->maxLength(100),
                                        ]),
                                ]),

                            CmsFields::document(label: __('admin.labels.fee_attachment'))
                                ->storeFileNamesIn('file_name'),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                Group::make()->schema([
                    Section::make(__('admin.sections.visual_display'))
                        ->description(__('admin.descriptions.visual_display'))
                        ->schema([
                            Select::make('icon')
                                ->label(__('admin.labels.icon_choice'))
                                ->options([
                                    'file-text' => __('admin.fee_icons.file_text'),
                                    'building' => __('admin.fee_icons.building'),
                                    'backpack' => __('admin.fee_icons.backpack'),
                                    'book-open' => __('admin.fee_icons.book_open'),
                                    'graduation-cap' => __('admin.fee_icons.graduation_cap'),
                                    'award' => __('admin.fee_icons.award'),
                                    'credit-card' => __('admin.fee_icons.credit_card'),
                                ])
                                ->default('file-text')
                                ->required()
                                ->native(false),

                            Select::make('icon_color')
                                ->label(__('admin.labels.icon_background_color'))
                                ->options([
                                    'green' => __('admin.colors.green'),
                                    'blue' => __('admin.colors.blue'),
                                    'pink' => __('admin.colors.pink'),
                                    'amber' => __('admin.colors.amber'),
                                ])
                                ->default('green')
                                ->required()
                                ->native(false),
                        ]),

                    Section::make(__('admin.sections.status_order'))
                        ->schema([
                            TextInput::make('sort_order')
                                ->label(__('admin.labels.order_number'))
                                ->numeric()
                                ->default(0)
                                ->helperText(__('admin.helpers.table_order')),

                            Toggle::make('is_active')
                                ->label(__('admin.fields.active_status'))
                                ->default(true)
                                ->helperText(__('admin.helpers.inactive_hidden_public')),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gradeLevel.name')
                    ->label(__('admin.fields.school_level'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('admin.labels.fee_type'))
                    ->getStateUsing(fn (AdmissionFee $record): string => $record->trans('name'))
                    ->weight('bold')
                    ->searchable(query: fn ($query, $search) => $query->where('name', 'ilike', '%'.$search.'%')),

                TextColumn::make('amount')
                    ->label(__('admin.labels.amount'))
                    ->badge()
                    ->color('gray'),

                TextColumn::make('payment_period')
                    ->label(__('admin.labels.period'))
                    ->getStateUsing(fn (AdmissionFee $record): string => $record->trans('payment_period')),

                TextColumn::make('icon_color')
                    ->label(__('admin.labels.color_theme'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'green' => 'success',
                        'blue' => 'info',
                        'pink' => 'danger',
                        'amber' => 'warning',
                        default => 'gray',
                    }),

                IconColumn::make('has_note')
                    ->label(__('admin.labels.has_note'))
                    ->boolean()
                    ->getStateUsing(fn (AdmissionFee $record): bool => ! empty(trim($record->trans('note')))),

                TextColumn::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('admin.fields.active'))
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 'all'])
            ->filters([
                SelectFilter::make('grade_level_id')
                    ->label(__('admin.labels.level_filter'))
                    ->options(fn () => GradeLevel::pluck('name', 'id')),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdmissionFees::route('/'),
            'create' => CreateAdmissionFee::route('/create'),
            'edit' => EditAdmissionFee::route('/{record}/edit'),
        ];
    }
}
