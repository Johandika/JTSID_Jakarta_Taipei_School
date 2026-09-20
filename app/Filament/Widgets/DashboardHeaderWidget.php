<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\SubjectResources\SubjectResourceResource;
use Filament\Widgets\Widget;

class DashboardHeaderWidget extends Widget
{
    protected string $view = 'filament.widgets.dashboard-header-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array{canCreatePosts: bool, canCreateResources: bool, createPostUrl: string, createResourceUrl: string}
     */
    protected function getViewData(): array
    {
        return [
            'canCreatePosts' => PostResource::canCreate(),
            'canCreateResources' => SubjectResourceResource::canCreate(),
            'createPostUrl' => PostResource::getUrl('create'),
            'createResourceUrl' => SubjectResourceResource::getUrl('create'),
        ];
    }
}
