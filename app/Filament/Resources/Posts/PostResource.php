<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\CmsResource;
use App\Filament\Support\CmsFields;
use App\Models\Post;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostResource extends CmsResource
{
    protected static ?string $model = Post::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'News & Media';

    protected static ?string $pluralModelLabel = 'News & Media';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Utama')
                        ->description('Judul topik utama dan alamat URL berita')
                        ->schema([
                            TextInput::make('topic')
                                ->label('Topik / Judul Berita')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('slug')
                                ->label('Slug URL')
                                ->helperText('Format: huruf-kecil-tanda-hubung (contoh: pentas-seni-2026)')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(191)
                                ->rules(['regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/']),
                        ])
                        ->columns(2),

                    Section::make('Konten & Terjemahan')
                        ->description('Isi konten artikel dalam berbagai bahasa (Wajib menyertakan bahasa Inggris)')
                        ->schema([
                            CmsFields::translations('Judul Artikel'),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    CmsFields::publishing(),

                    Section::make('Klasifikasi & Sasaran')
                        ->description('Kategori, departemen, dan jenjang')
                        ->schema([
                            Select::make('category_id')
                                ->label('Kategori Berita')
                                ->relationship('category', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            CmsFields::department(),
                            CmsFields::schoolLevels(),
                        ]),

                    Section::make('Media & Lampiran')
                        ->description('Cover visual dan lampiran')
                        ->schema([
                            CmsFields::uploadDate(),
                            CmsFields::image('image', 4096, 'Gambar Cover Berita'),
                            CmsFields::document(false, 'File Dokumen (Opsional)'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('topic')
                    ->label('Topik Berita')
                    ->searchable()
                    ->weight('medium')
                    ->limit(40),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('school_level_label')
                    ->label('Jenjang'),
                TextColumn::make('department.name')
                    ->label('Departemen'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('upload_date')
                    ->label('Tanggal Unggah')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('translation_status')
                    ->label('Terjemahan')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
            ])
            ->defaultSort('upload_date', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
