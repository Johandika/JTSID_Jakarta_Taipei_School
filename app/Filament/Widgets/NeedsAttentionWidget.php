<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AnnouncementPopups\AnnouncementPopupResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\SubjectResources\SubjectResourceResource;
use App\Models\AnnouncementPopup;
use Filament\Widgets\Widget;

class NeedsAttentionWidget extends Widget
{
    protected string $view = 'filament.widgets.needs-attention-widget';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    /**
     * @return array{items: array<int, array{count: int, title: string, description: string, url: string, icon: string, tone: string}>}
     */
    public function getViewData(): array
    {
        $items = [
            [
                'count' => PostResource::getEloquentQuery()->where('status', 'draft')->count(),
                'title' => __('admin.widgets.draft_news_title'),
                'description' => __('admin.widgets.draft_news_desc'),
                'url' => PostResource::getUrl('index'),
                'icon' => 'heroicon-m-pencil-square',
                'tone' => 'primary',
            ],
            [
                'count' => SubjectResourceResource::getEloquentQuery()->where('status', 'draft')->count(),
                'title' => __('admin.widgets.draft_resources_title'),
                'description' => __('admin.widgets.draft_resources_desc'),
                'url' => SubjectResourceResource::getUrl('index'),
                'icon' => 'heroicon-m-folder-open',
                'tone' => 'primary',
            ],
        ];

        if (AnnouncementPopupResource::canViewAny()) {
            $items[] = [
                'count' => AnnouncementPopup::visible()->count(),
                'title' => __('admin.widgets.visible_popup_title'),
                'description' => __('admin.widgets.visible_popup_desc'),
                'url' => AnnouncementPopupResource::getUrl('index'),
                'icon' => 'heroicon-m-megaphone',
                'tone' => 'warning',
            ];
        }

        return [
            'items' => $items,
        ];
    }
}
