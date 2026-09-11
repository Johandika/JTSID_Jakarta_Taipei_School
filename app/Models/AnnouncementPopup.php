<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolLevels;
use App\Support\SchoolLevel;
use Database\Factories\AnnouncementPopupFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnnouncementPopup extends CmsModel
{
    /** @use HasFactory<AnnouncementPopupFactory> */
    use HasFactory;

    use HasSchoolLevels;

    protected function casts(): array
    {
        return [...parent::casts(), 'starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->when(SchoolLevel::selected() === 'all',
                fn (Builder $query) => $query->forAllSchoolLevels(),
                fn (Builder $query) => $query->forSchoolLevel());
    }
}
