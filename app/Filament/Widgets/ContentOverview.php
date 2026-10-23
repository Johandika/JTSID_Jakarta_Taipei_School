<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\HeroSlides\HeroSlideResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\StaffProfiles\StaffProfileResource;
use App\Filament\Resources\SubjectResources\SubjectResourceResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $stats = [];
        $canViewPosts = PostResource::canViewAny();

        if ($canViewPosts) {
            $posts = PostResource::getEloquentQuery();
            $publishedPosts = (clone $posts)->published()->count();
            $draftPosts = (clone $posts)->where('status', 'draft')->count();

            $stats[] = Stat::make(__('admin.widgets.published_news'), number_format($publishedPosts))
                ->description(__('admin.widgets.published_news_desc'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success');
        }

        if (SubjectResourceResource::canViewAny()) {
            $publishedResources = SubjectResourceResource::getEloquentQuery()->published()->count();

            $stats[] = Stat::make(__('admin.widgets.resources'), number_format($publishedResources))
                ->description(__('admin.widgets.resources_desc'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray');
        }

        if (StaffProfileResource::canViewAny()) {
            $stats[] = Stat::make(__('admin.widgets.staff'), number_format(StaffProfileResource::getEloquentQuery()->where('is_active', true)->count()))
                ->description(__('admin.widgets.staff_desc'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('gray');
        }

        if (HeroSlideResource::canViewAny()) {
            $stats[] = Stat::make('Hero Slides', number_format(HeroSlideResource::getEloquentQuery()->where('is_active', true)->count()))
                ->description(__('admin.widgets.hero_slides_desc'))
                ->descriptionIcon('heroicon-m-photo')
                ->color('gray');
        }

        if ($canViewPosts) {
            $stats[] = Stat::make(__('admin.widgets.drafts'), number_format($draftPosts))
                ->description($draftPosts > 0 ? __('admin.widgets.drafts_pending') : __('admin.widgets.drafts_clear'))
                ->descriptionIcon($draftPosts > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle')
                ->color($draftPosts > 0 ? 'primary' : 'success')
                ->extraAttributes([
                    'class' => 'stat-card-featured',
                ]);
        }

        return $stats;
    }
}
