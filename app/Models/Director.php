<?php

namespace App\Models;

use App\Support\Locale;
use Database\Factories\DirectorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Director extends CmsModel
{
    /** @use HasFactory<DirectorFactory> */
    use HasFactory;

    protected $table = 'directors';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'name' => 'array',
            'role' => 'array',
            'education' => 'array',
            'bio' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);
    }

    /**
     * Get localized value for a translatable JSON field, falling back gracefully.
     */
    public function trans(string $field, ?string $locale = null): string
    {
        $locale = Locale::content($locale);
        $value = $this->{$field};

        if (! is_array($value)) {
            return (string) ($value ?? '');
        }

        return (string) ($value[$locale] ?? $value['id'] ?? $value['en'] ?? (reset($value) ?: ''));
    }

    /**
     * Get localized education entries as an array of strings.
     *
     * @return array<int, string>
     */
    public function getEducationList(?string $locale = null): array
    {
        $locale = Locale::content($locale);
        $val = $this->education;

        if (! is_array($val)) {
            return [];
        }

        $items = $val[$locale] ?? $val['id'] ?? $val['en'] ?? (reset($val) ?: []);

        if (is_string($items)) {
            return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $items))));
        }

        return is_array($items) ? array_values(array_filter($items)) : [];
    }

    /**
     * Resolve the photo URL (supporting public path, uploaded storage, or fallback).
     */
    public function getPhotoUrlAttribute(): string
    {
        if (empty($this->photo)) {
            return asset('images/logo.webp');
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        if (file_exists(public_path($this->photo))) {
            return asset($this->photo);
        }

        return asset('storage/'.$this->photo);
    }
}
