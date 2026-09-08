<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait RecordsActivity
{
    protected static function bootRecordsActivity(): void
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            static::$event(function (Model $model) use ($event) {
                AuditLog::create([
                    'user_id' => Auth::id(), 'action' => $event,
                    'entity_type' => class_basename($model), 'entity_id' => $model->id,
                    'ip_address' => app()->runningInConsole() ? null : request()->ip(),
                    'changed_fields' => array_values(array_diff(array_keys($model->getChanges()), ['password', 'remember_token'])),
                ]);
            });
        }
    }
}
