<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\SubjectResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $posts = Post::query();
        $resources = SubjectResource::query();
        if (auth()->user()?->role?->code === 'department_admin') {
            $posts->where('department_id', auth()->user()->department_id);
            $resources->where('department_id', auth()->user()->department_id);
        }

        return [
            Stat::make('Published news', (clone $posts)->published()->count())->icon('heroicon-o-newspaper')->color('success'),
            Stat::make('Published resources', $resources->published()->count())->icon('heroicon-o-folder-open'),
            Stat::make('News awaiting publication', $posts->where('status', 'draft')->count())->icon('heroicon-o-pencil-square')->color('warning'),
        ];
    }
}
