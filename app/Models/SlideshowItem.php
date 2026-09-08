<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SlideshowItem extends CmsModel
{
    public function save(array $options = [])
    {
        return DB::transaction(function () use ($options) {
            DB::select('SELECT pg_advisory_xact_lock(90412026)');
            if (($this->is_active ?? true) && static::where('is_active', true)->when($this->exists, fn ($q) => $q->whereKeyNot($this->id))->count() >= 10) {
                throw ValidationException::withMessages(['is_active' => 'A maximum of 10 active slides is allowed. Disable an existing slide first.']);
            }

            return parent::save($options);
        });
    }
}
