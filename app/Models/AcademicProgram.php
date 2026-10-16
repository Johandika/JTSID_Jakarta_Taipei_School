<?php

namespace App\Models;

use App\Support\Locale;
use Database\Factories\AcademicProgramFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicProgram extends CmsModel
{
    /** @use HasFactory<AcademicProgramFactory> */
    use HasFactory;

    protected $table = 'academic_programs';

    protected $fillable = [
        'image',
        'label',
        'title',
        'description',
        'url',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'label' => 'array',
            'title' => 'array',
            'description' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);
    }

    /**
     * Get localized value for a translatable JSON field.
     */
    public function trans(string $field, ?string $locale = null): string
    {
        $locale = Locale::content($locale);
        $value = $this->{$field};

        if (! is_array($value)) {
            return (string) ($value ?? '');
        }

        return (string) ($value[$locale] ?? $value['en'] ?? $value['id'] ?? (reset($value) ?: ''));
    }

    /**
     * Get asset URL for uploaded or static image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        $clean = ltrim($this->image, '/');
        if (file_exists(public_path($clean))) {
            return asset($clean);
        }

        return asset('storage/'.$clean);
    }

    /**
     * Scope for active records.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
