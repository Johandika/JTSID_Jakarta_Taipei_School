<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'lg' => 3,
        ];
    }
}
