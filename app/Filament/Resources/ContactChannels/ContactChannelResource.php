<?php

namespace App\Filament\Resources\ContactChannels;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\ContactChannel;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactChannelResource extends CmsResource
{
    protected static ?string $model = ContactChannel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Contacts & QR';

    protected static ?string $pluralModelLabel = 'Contacts & QR';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('channel')->options(array_combine(['admission_tel', 'phone', 'instagram', 'whatsapp', 'tiktok', 'address', 'email'], ['Admission telephone', 'Telephone', 'Instagram', 'WhatsApp', 'TikTok', 'Address', 'Email']))->required()->unique(ignoreRecord: true),
            TextInput::make('label')->required()->maxLength(150), TextInput::make('value')->required()->maxLength(255),
            CmsFields::image('qr_image', 1024), Toggle::make('is_visible_header'), Toggle::make('is_visible_footer')->default(true),
            TextInput::make('sort_order')->integer()->minValue(0)->default(0)->required()])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('channel')->badge(), TextColumn::make('label'), TextColumn::make('value')->limit(55)->searchable(), IconColumn::make('is_visible_footer')->boolean()])

            ->defaultSort('id', 'desc')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactChannels::route('/'),
            'create' => Pages\CreateContactChannel::route('/create'),
            'edit' => Pages\EditContactChannel::route('/{record}/edit'),
        ];
    }
}
