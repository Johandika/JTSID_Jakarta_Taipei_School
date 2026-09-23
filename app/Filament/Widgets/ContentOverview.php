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
        $posts = PostResource::getEloquentQuery();
        $resources = SubjectResourceResource::getEloquentQuery();

        $publishedPosts = (clone $posts)->published()->count();
        $publishedResources = (clone $resources)->published()->count();
        $draftPosts = (clone $posts)->where('status', 'draft')->count();

        $stats = [
            Stat::make('Berita Publikasi', number_format($publishedPosts))
                ->description('Artikel tayang di website')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Materi Pelajaran', number_format($publishedResources))
                ->description('Silabus & materi aktif')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

        ];

        if (StaffProfileResource::canViewAny()) {
            $stats[] = Stat::make('Staf & Guru', number_format(StaffProfileResource::getEloquentQuery()->where('is_active', true)->count()))
                ->description('Profil pengajar terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('gray');
        }

        if (HeroSlideResource::canViewAny()) {
            $stats[] = Stat::make('Hero Slides', number_format(HeroSlideResource::getEloquentQuery()->where('is_active', true)->count()))
                ->description('Slide carousel aktif')
                ->descriptionIcon('heroicon-m-photo')
                ->color('gray');
        }

        $stats[] = Stat::make('Draft Perlu Ditinjau', number_format($draftPosts))
            ->description($draftPosts > 0 ? 'Menunggu persetujuan / rilis' : 'Seluruh berita telah tayang')
            ->descriptionIcon($draftPosts > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle')
            ->color($draftPosts > 0 ? 'primary' : 'success')
            ->extraAttributes([
                'class' => 'stat-card-featured',
            ]);

        return $stats;
    }
}
