<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class HeroSlide extends CmsModel
{
    use HasFactory;

    protected $table = 'hero_slides';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'title' => 'array',
            'description' => 'array',
            'tagline' => 'array',
            'cta_text' => 'array',
            'sort_order' => 'integer',
        ]);
    }

    /**
     * Get a localized field value for the current locale, falling back to English.
     */
    public function trans(string $field): string
    {
        $value = $this->{$field};
        if (! is_array($value)) {
            return (string) ($value ?? '');
        }

        $locale = app()->getLocale();

        return (string) ($value[$locale] ?? $value['en'] ?? (reset($value) ?: ''));
    }
}
