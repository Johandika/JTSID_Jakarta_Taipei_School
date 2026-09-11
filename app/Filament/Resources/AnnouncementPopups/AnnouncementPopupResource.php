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
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(191),
                Toggle::make('is_active')->default(false)->helperText('Enable when ready to display to visitors.'),
                Textarea::make('body')->label('Message')->rows(4)->maxLength(5000)->columnSpanFull(),
                CmsFields::image()->helperText('Optional poster for congratulations, events, or announcements.'),
                CmsFields::safeUrl('link_url')->label('Read more link'),
                CmsFields::schoolLevels(),
                DateTimePicker::make('starts_at')->seconds(false)->label('Start time ('.config('app.timezone').')'),
                DateTimePicker::make('ends_at')->seconds(false)
                    ->after(fn (Get $get): ?string => filled($get('starts_at')) ? 'starts_at' : null)
                    ->label('End time ('.config('app.timezone').')'),
                TextInput::make('sort_order')->label('Display priority')->integer()->minValue(0)->default(0)->required()
                    ->helperText('Lower numbers appear first. If priorities match, the newest popup is used. Each popup appears once per browser tab session.'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('school_level_label')->label('School levels'),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('starts_at')->dateTime(),
                TextColumn::make('ends_at')->dateTime(),
                TextColumn::make('sort_order')->label('Priority')->sortable(),
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
