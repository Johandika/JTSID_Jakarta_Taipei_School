<?php

namespace App\Filament\Resources;

use App\Support\DepartmentPermission;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Lang;

abstract class CmsResource extends Resource
{
    public static function getNavigationLabel(): string
    {
        return static::resourceTranslation('navigation') ?? parent::getNavigationLabel();
    }

    public static function getModelLabel(): string
    {
        return static::resourceTranslation('model') ?? parent::getModelLabel();
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if (in_array($user->role?->code, ['super_admin', 'content_admin'], true)) {
            return true;
        }

        if ($user->role?->code === 'department_admin') {
            $model = static::getModel();

            return $model ? DepartmentPermission::canAccessModel($user, $model) : false;
        }

        return parent::canViewAny();
    }

    public static function getPluralModelLabel(): string
    {
        return static::resourceTranslation('plural') ?? parent::getPluralModelLabel();
    }

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

    private static function resourceTranslation(string $key): ?string
    {
        $translationKey = 'admin.resources.'.class_basename(static::class).'.'.$key;

        return Lang::has($translationKey) ? __($translationKey) : null;
    }
}
