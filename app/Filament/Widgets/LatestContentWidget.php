<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestContentWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Publikasi Berita & Media Terbaru')
            ->query(PostResource::getEloquentQuery()->latest('upload_date'))
            ->columns([
                TextColumn::make('topic')
                    ->label('Topik Berita')
                    ->searchable()
                    ->limit(40)
                    ->weight('semibold'),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('upload_date')
                    ->label('Tanggal Unggah')
                    ->date('d M Y'),
            ])
            ->recordUrl(fn (Post $record): ?string => PostResource::canEdit($record)
                ? PostResource::getUrl('edit', ['record' => $record])
                : null)
            ->paginated([5]);
    }
}
