<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolLevels;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubjectResource extends CmsModel
{
    use HasSchoolLevels;
    use HasTranslations;

    public const DETAILED_TYPE_CODES = ['calendar', 'syllabus', 'textbook'];

    public const CLASS_LEVEL_OPTIONS = [
        'tk1' => 'TK1',
        'tk2' => 'TK2',
        'sd_1' => 'SD 1',
        'sd_2' => 'SD 2',
        'sd_3' => 'SD 3',
        'sd_4' => 'SD 4',
        'sd_5' => 'SD 5',
        'sd_6' => 'SD 6',
        'smp_1' => 'SMP 1',
        'smp_2' => 'SMP 2',
        'smp_3' => 'SMP 3',
        'sma_1' => 'SMA 1',
        'sma_2' => 'SMA 2',
        'sma_3' => 'SMA 3',
    ];

    public const SEMESTER_OPTIONS = [
        'semester_1' => 'Semester 1',
        'semester_2' => 'Semester 2',
    ];

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

            if (! $resource->usesDetailedAcademicPeriod()) {
                $resource->class_level = null;
                $resource->semester = null;

                return;
            }

            $resource->school_levels = [];
            $resource->grade_level_id = null;

            Validator::make([
                'class_level' => $resource->class_level,
                'semester' => $resource->semester,
            ], [
                'class_level' => ['required', 'string', Rule::in(array_keys(self::CLASS_LEVEL_OPTIONS))],
                'semester' => ['required', 'string', Rule::in(array_keys(self::SEMESTER_OPTIONS))],
            ])->validate();
        });
    }

    public static function detailedTypeIds(): array
    {
        return SubjectResourceType::whereIn('code', self::DETAILED_TYPE_CODES)->pluck('id')->all();
    }

    public function usesDetailedAcademicPeriod(): bool
    {
        $typeCode = $this->relationLoaded('type') ? $this->type?->code : SubjectResourceType::whereKey($this->type_id)->value('code');

        return in_array($typeCode, self::DETAILED_TYPE_CODES, true);
    }

    public function scopeForClassLevel(Builder $query, ?string $classLevel): Builder
    {
        if (! array_key_exists((string) $classLevel, self::CLASS_LEVEL_OPTIONS)) {
            return $query;
        }

        return $query->where('class_level', $classLevel);
    }

    public function scopeForSemester(Builder $query, ?string $semester): Builder
    {
        if (! array_key_exists((string) $semester, self::SEMESTER_OPTIONS)) {
            return $query;
        }

        return $query->where('semester', $semester);
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

    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/'.$this->image) : asset('images/campus.jpg');
    }

    public function getClassLevelLabelAttribute(): ?string
    {
        return self::CLASS_LEVEL_OPTIONS[$this->class_level] ?? null;
    }

    public function getSemesterLabelAttribute(): ?string
    {
        return self::SEMESTER_OPTIONS[$this->semester] ?? null;
    }

    public function getResourceLevelLabelAttribute(): ?string
    {
        if ($this->class_level_label && $this->semester_label) {
            return $this->class_level_label.' - '.$this->semester_label;
        }

        return null;
    }
}
