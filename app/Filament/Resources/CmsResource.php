<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

abstract class CmsResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (method_exists($query->getModel(), 'translations')) {
            $query->with('translations');
        }
        if (auth()->user()?->role?->code === 'department_admin' && method_exists($query->getModel(), 'department')) {
            $query->where('department_id', auth()->user()->department_id);
        }

        return $query;
    }
}
