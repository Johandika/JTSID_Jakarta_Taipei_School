<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facility extends CmsModel
{
    use HasFactory;

    protected $table = 'facilities';

    public const CATEGORIES = [
        'sekolah' => [
            'id' => 'Fasilitas Sekolah',
            'en' => 'School Facilities',
            'zh-TW' => '校園設施',
        ],
        'asrama_guru' => [
            'id' => 'Asrama Guru',
            'en' => 'Teacher Dormitory',
            'zh-TW' => '教師宿舍',
        ],
        'tk' => [
            'id' => 'Taman Kanak-Kanak (TK)',
            'en' => 'Kindergarten (TK)',
            'zh-TW' => '幼兒園 (TK)',
        ],
        'sd' => [
            'id' => 'Sekolah Dasar (SD)',
            'en' => 'Elementary School (SD)',
            'zh-TW' => '小學部 (SD)',
        ],
        'smp' => [
            'id' => 'Sekolah Menengah Pertama (SMP)',
            'en' => 'Junior High School (SMP)',
            'zh-TW' => '國中部 (SMP)',
        ],
        'sma' => [
            'id' => 'Sekolah Menengah Atas (SMA)',
            'en' => 'Senior High School (SMA)',
            'zh-TW' => '高中部 (SMA)',
        ],
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'name' => 'array',
        ]);
    }

    /**
     * Get key => localized name options for Filament select.
     *
     * @return array<string, string>
     */
    public static function getCategoryOptions(): array
    {
        $locale = app()->getLocale();
        if ($locale === 'zh') {
            $locale = 'zh-TW';
        }

        $options = [];
        foreach (self::CATEGORIES as $key => $labels) {
            $options[$key] = $labels[$locale] ?? $labels['id'] ?? $labels['en'] ?? $key;
        }

        return $options;
    }

    /**
     * Get localized label for a category key.
     */
    public static function getCategoryLabel(string $key, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        if ($locale === 'zh') {
            $locale = 'zh-TW';
        }

        return self::CATEGORIES[$key][$locale]
            ?? self::CATEGORIES[$key]['id']
            ?? self::CATEGORIES[$key]['en']
            ?? $key;
    }

    /**
     * Category label accessor.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::getCategoryLabel($this->category ?? '');
    }

    /**
     * Get the full URL for the image.
     */
    public function getImageUrlAttribute(): string
    {
        $image = $this->image;
        if (blank($image)) {
            return asset('images/campus.webp');
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        $cleanPath = ltrim($image, '/');
        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        return asset('storage/'.$cleanPath);
    }

    /**
     * Get localized name.
     */
    public function trans(string $field = 'name'): string
    {
        $value = $this->{$field};
        if (! is_array($value)) {
            return (string) ($value ?? '');
        }

        $locale = app()->getLocale();
        if ($locale === 'zh') {
            $locale = 'zh-TW';
        }

        return (string) ($value[$locale] ?? $value['en'] ?? (reset($value) ?: ''));
    }
}
