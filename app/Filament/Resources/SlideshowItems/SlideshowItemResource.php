<?php

namespace App\Filament\Resources\SlideshowItems;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\SlideshowItem;
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

class SlideshowItemResource extends CmsResource
{
    protected static ?string $model = SlideshowItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Facilities & Highlights';

    protected static ?string $pluralModelLabel = 'Facilities & Highlights';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Fasilitas & Sarana')
                        ->description('Nama sarana, label kategori, dan tautan info detail')
                        ->schema([
                            TextInput::make('topic')
                                ->label('Nama Fasilitas / Sarana')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(191),
                            TextInput::make('tag')
                                ->label('Label Kategori')
                                ->placeholder('Contoh: PEMBELAJARAN, EKSPLORASI, KREATIVITAS, LINGKUNGAN')
                                ->maxLength(50),
                            CmsFields::safeUrl('link_url', 'Link URL Tujuan (Opsional)'),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Foto Fasilitas')
                        ->description('Foto resolusi tinggi untuk carousel homepage')
                        ->schema([
                            CmsFields::image('image', 2048, 'Gambar Sarana')->required(),
                        ]),

                    Section::make('Status & Urutan')
                        ->schema([
                            TextInput::make('sort_order')
                                ->label('Urutan Tampilan')
                                ->integer()
                                ->minValue(0)
                                ->default(0)
                                ->required(),
                            Toggle::make('is_active')
                                ->label('Aktif / Tampilkan di Carousel')
                                ->default(true),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image')
                ->label('Foto')
                ->disk('public'),
            TextColumn::make('tag')
                ->label('Kategori')
                ->badge()
                ->placeholder('—'),
            TextColumn::make('topic')
                ->label('Nama Fasilitas')
                ->searchable()
                ->weight('medium'),
            TextColumn::make('sort_order')
                ->label('Urutan')
                ->sortable(),
            IconColumn::make('is_active')
                ->label('Aktif')
                ->boolean(),
        ])
            ->defaultSort('sort_order', 'asc')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlideshowItems::route('/'),
            'create' => Pages\CreateSlideshowItem::route('/create'),
            'edit' => Pages\EditSlideshowItem::route('/{record}/edit'),
        ];
    }
}
