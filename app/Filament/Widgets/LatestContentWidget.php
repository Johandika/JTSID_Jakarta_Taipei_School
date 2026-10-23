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

    public static function canView(): bool
    {
        return PostResource::canViewAny();
    }

    public function mount(): void
    {
        abort_unless(static::canView(), 403);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('admin.widgets.latest_news'))
            ->query(PostResource::getEloquentQuery()->latest('upload_date'))
            ->columns([
                TextColumn::make('topic')
                    ->label(__('admin.fields.news_topic'))
                    ->searchable()
                    ->limit(40)
                    ->weight('semibold'),
                TextColumn::make('category.name')
                    ->label(__('admin.fields.category'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge(),
                TextColumn::make('upload_date')
                    ->label(__('admin.fields.upload_date'))
                    ->date('d M Y'),
            ])
            ->recordUrl(fn (Post $record): ?string => PostResource::canEdit($record)
                ? PostResource::getUrl('edit', ['record' => $record])
                : null)
            ->paginated([5]);
    }
}
