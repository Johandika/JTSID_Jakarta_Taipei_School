<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use App\Support\DepartmentPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

abstract class CmsModel extends Model
{
    use RecordsActivity;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'published_at' => 'datetime', 'upload_date' => 'date'];
    }

    protected static function booted(): void
    {
        static::saving(function (Model $model) {
            $user = Auth::user();
            if ($user && array_key_exists('department_id', $model->getAttributes()) && DepartmentPermission::isDepartmentRole($user->role?->code)) {
                if ((int) $model->department_id !== (int) $user->department_id
                    || ($model->exists && (int) $model->getOriginal('department_id') !== (int) $user->department_id)) {
                    throw ValidationException::withMessages(['department_id' => 'You can only manage your own department.']);
                }
            }
            if (array_key_exists('status', $model->getAttributes())) {
                $model->updated_by = $user?->id ?? $model->updated_by;
                if (! $model->exists) {
                    $model->created_by = $user?->id ?? $model->created_by;
                }
            }
        });
    }
}
