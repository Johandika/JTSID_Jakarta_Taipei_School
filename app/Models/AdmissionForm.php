<?php

namespace App\Models;

use App\Support\SchoolLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AdmissionForm extends Model
{
    protected $guarded = ['id'];

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

    public static function current(string $schoolLevel = 'tk'): ?self
    {
        return self::active()
            ->forLevel($schoolLevel)
            ->latest('updated_at')
            ->get()
            ->first(fn (self $form): bool => $form->isDownloadable());
    }

    /**
     * @return \Illuminate\Support\Collection<string, self>
     */
    public static function downloadableByLevel(): Collection
    {
        return self::active()
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
