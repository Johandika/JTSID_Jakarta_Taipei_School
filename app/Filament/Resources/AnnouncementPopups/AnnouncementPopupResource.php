<?php

namespace App\Filament\Resources\AnnouncementPopups;

use App\Filament\Resources\AnnouncementPopups\Pages\CreateAnnouncementPopup;
use App\Filament\Resources\AnnouncementPopups\Pages\EditAnnouncementPopup;
use App\Filament\Resources\AnnouncementPopups\Pages\ListAnnouncementPopups;
use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\AnnouncementPopup;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementPopupResource extends CmsResource
{
    protected static ?string $model = AnnouncementPopup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Announcement Popups';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make(__('admin.sections.announcement_content'))
                        ->description(__('admin.descriptions.announcement_content'))
                        ->schema([
                            TextInput::make('title')
                                ->label(__('admin.labels.announcement_title'))
                                ->required()
                                ->maxLength(191),
                            Textarea::make('body')
                                ->label(__('admin.labels.full_message'))
                                ->rows(4)
                                ->maxLength(5000)
                                ->columnSpanFull(),
                            CmsFields::image('image', 4096, __('admin.labels.announcement_poster'))
                                ->helperText(__('admin.helpers.announcement_poster')),
                            CmsFields::safeUrl('link_url', __('admin.labels.read_more_link')),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make(__('admin.sections.schedule_popup_status'))
                        ->description(__('admin.descriptions.schedule_popup_status'))
                        ->schema([
                            Toggle::make('is_active')
                                ->label(__('admin.labels.activate_popup'))
                                ->default(false)
                                ->helperText(__('admin.helpers.activate_popup')),
                            DateTimePicker::make('starts_at')
                                ->seconds(false)
                                ->label(__('admin.labels.starts_at', ['timezone' => config('app.timezone')])),
                            DateTimePicker::make('ends_at')
                                ->seconds(false)
                                ->after(fn (Get $get): ?string => filled($get('starts_at')) ? 'starts_at' : null)
                                ->label(__('admin.labels.ends_at', ['timezone' => config('app.timezone')])),
                            TextInput::make('sort_order')
                                ->label(__('admin.labels.display_priority'))
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required()
                                ->helperText(__('admin.helpers.display_priority')),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.labels.announcement_title'))
                    ->searchable()
                    ->weight('medium'),
                IconColumn::make('is_active')
                    ->label(__('admin.fields.active_status'))
                    ->boolean(),
                TextColumn::make('starts_at')
                    ->label(__('admin.labels.starts'))
                    ->dateTime('d M Y, H:i'),
                TextColumn::make('ends_at')
                    ->label(__('admin.labels.ends'))
                    ->dateTime('d M Y, H:i'),
                TextColumn::make('sort_order')
                    ->label(__('admin.labels.priority'))
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncementPopups::route('/'),
            'create' => CreateAnnouncementPopup::route('/create'),
            'edit' => EditAnnouncementPopup::route('/{record}/edit'),
        ];
    }
}
