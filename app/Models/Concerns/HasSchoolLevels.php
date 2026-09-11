<?php

namespace App\Models\Concerns;

use App\Support\SchoolLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait HasSchoolLevels
{
    public function initializeHasSchoolLevels(): void
    {
        $this->mergeCasts(['school_levels' => 'array']);
    }

    protected static function bootHasSchoolLevels(): void
    {
        static::saving(function (Model $model): void {
            Validator::make(['school_levels' => $model->school_levels], [
                'school_levels' => ['nullable', 'array'],
                'school_levels.*' => ['string', 'distinct', Rule::in(array_keys(SchoolLevel::OPTIONS))],
            ])->validate();
        });
    }

    public function scopeForSchoolLevel(Builder $query, ?string $level = null): Builder
    {
        $level ??= SchoolLevel::selected();
        if (! array_key_exists($level, SchoolLevel::OPTIONS)) {
            return $query;
        }

        return $query->where(fn (Builder $query) => $query->forAllSchoolLevels()->orWhereJsonContains('school_levels', $level));
    }

    public function scopeForAllSchoolLevels(Builder $query): Builder
    {
        return $query->where(fn (Builder $query) => $query->whereNull('school_levels')->orWhereJsonLength('school_levels', 0));
    }

    public function getSchoolLevelLabelAttribute(): string
    {
        return empty($this->school_levels) ? __('site.school_wide') : implode(', ', array_map(
            fn (string $level): string => SchoolLevel::OPTIONS[$level], $this->school_levels,
        ));
    }
}
