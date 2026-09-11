<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolLevels;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectResource extends CmsModel
{
    use HasSchoolLevels;
    use HasTranslations;

    protected static function booted(): void
    {
        parent::booted();
        static::saving(function (self $resource): void {
            if ($resource->school_levels === null && $resource->grade_level_id) {
                $resource->school_levels = [GradeLevel::findOrFail($resource->grade_level_id)->code];
            }
            if ($resource->isDirty('school_levels')) {
                $resource->grade_level_id = count($resource->school_levels ?? []) === 1
                    ? GradeLevel::where('code', $resource->school_levels[0])->value('id') : null;
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(SubjectResourceType::class);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }
}
