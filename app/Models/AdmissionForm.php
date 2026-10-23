<?php

namespace App\Models;

use App\Support\SchoolLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AdmissionForm extends Model
{
    public const NATIONALITIES = ['wni', 'wna'];

    protected $guarded = ['id'];

    protected $attributes = ['nationality' => 'wni'];

    /**
     * @return array<string, string>
     */
    public static function nationalityOptions(): array
    {
        return collect(self::NATIONALITIES)
            ->mapWithKeys(fn (string $nationality): array => [$nationality => __('admin.nationalities.'.$nationality)])
            ->all();
    }

    /**
     * @return array{school_level: string, nationality: string}|null
     */
    public static function nextAvailableSlot(): ?array
    {
        $existingForms = self::query()->get(['school_level', 'nationality'])
            ->mapWithKeys(fn (self $form): array => [$form->school_level.':'.$form->nationality => true]);

        foreach (self::NATIONALITIES as $nationality) {
            foreach (array_keys(SchoolLevel::OPTIONS) as $schoolLevel) {
                if (! $existingForms->has($schoolLevel.':'.$nationality)) {
                    return ['school_level' => $schoolLevel, 'nationality' => $nationality];
                }
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForLevel(Builder $query, string $schoolLevel): Builder
    {
        return $query->where('school_level', $schoolLevel);
    }

    public static function current(string $schoolLevel = 'tk', string $nationality = 'wni'): ?self
    {
        return self::active()
            ->forLevel($schoolLevel)
            ->where('nationality', $nationality)
            ->latest('updated_at')
            ->get()
            ->first(fn (self $form): bool => $form->isDownloadable());
    }

    /**
     * @return Collection<string, self>
     */
    public static function downloadableByLevel(string $nationality = 'wni'): Collection
    {
        return self::active()
            ->where('nationality', $nationality)
            ->whereIn('school_level', array_keys(SchoolLevel::OPTIONS))
            ->latest('updated_at')
            ->get()
            ->filter(fn (self $form): bool => $form->isDownloadable())
            ->unique('school_level')
            ->keyBy('school_level');
    }

    public function isDownloadable(): bool
    {
        return filled($this->file_path)
            && ! str_contains($this->file_path, '..')
            && Storage::disk('public')->exists($this->file_path);
    }

    public function getDownloadUrlAttribute(): string
    {
        return asset('storage/'.$this->file_path);
    }

    public function getDownloadNameAttribute(): string
    {
        return str($this->title ?: 'form-pendaftaran')->slug().'.'.pathinfo($this->file_path, PATHINFO_EXTENSION);
    }

    public function getSchoolLevelLabelAttribute(): string
    {
        return SchoolLevel::OPTIONS[$this->school_level] ?? strtoupper((string) $this->school_level);
    }
}
