<?php

namespace App\Models;

use App\Support\Locale;
use Database\Factories\CurriculumPillarFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CurriculumPillar extends CmsModel
{
    /** @use HasFactory<CurriculumPillarFactory> */
    use HasFactory;

    protected $table = 'curriculum_pillars';

    public const COLOR_THEMES = [
        'red' => [
            'label' => 'Red (Taiwan Theme)',
            'icon_bg' => 'bg-[#fdf2f2]',
            'badge_bg' => 'bg-[#fef2f2]',
            'badge_text' => 'text-[#e02424]',
            'badge_border' => 'border-[#fecaca]/70',
            'card_hover' => 'hover:border-red-400/40',
        ],
        'green' => [
            'label' => 'Green (Indonesia Theme)',
            'icon_bg' => 'bg-[#edfdf5]',
            'badge_bg' => 'bg-[#ecfdf5]',
            'badge_text' => 'text-[#10b981]',
            'badge_border' => 'border-[#a7f3d0]/70',
            'card_hover' => 'hover:border-emerald-500/40',
        ],
        'blue' => [
            'label' => 'Blue (English / Cambridge Theme)',
            'icon_bg' => 'bg-[#eff6ff]',
            'badge_bg' => 'bg-[#eff6ff]',
            'badge_text' => 'text-[#3b82f6]',
            'badge_border' => 'border-[#bfdbfe]/70',
            'card_hover' => 'hover:border-blue-400/40',
        ],
        'emerald' => [
            'label' => 'Emerald (JTS Brand)',
            'icon_bg' => 'bg-[#eaf4ee]',
            'badge_bg' => 'bg-[#eaf4ee]',
            'badge_text' => 'text-[#176443]',
            'badge_border' => 'border-[#b8deca]/70',
            'card_hover' => 'hover:border-[#176443]/40',
        ],
        'amber' => [
            'label' => 'Amber / Gold',
            'icon_bg' => 'bg-[#fffbeb]',
            'badge_bg' => 'bg-[#fffbeb]',
            'badge_text' => 'text-[#d97706]',
            'badge_border' => 'border-[#fde68a]/70',
            'card_hover' => 'hover:border-amber-400/40',
        ],
        'purple' => [
            'label' => 'Purple / Global',
            'icon_bg' => 'bg-[#faf5ff]',
            'badge_bg' => 'bg-[#faf5ff]',
            'badge_text' => 'text-[#9333ea]',
            'badge_border' => 'border-[#e9d5ff]/70',
            'card_hover' => 'hover:border-purple-400/40',
        ],
    ];

    /**
     * Get localized options array for color theme select dropdown.
     *
     * @return array<string, string>
     */
    public static function getColorThemeOptions(): array
    {
        return [
            'red' => __('admin.curriculum_pillars.themes.red'),
            'green' => __('admin.curriculum_pillars.themes.green'),
            'blue' => __('admin.curriculum_pillars.themes.blue'),
            'emerald' => __('admin.curriculum_pillars.themes.emerald'),
            'amber' => __('admin.curriculum_pillars.themes.amber'),
            'purple' => __('admin.curriculum_pillars.themes.purple'),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'title' => 'array',
            'description' => 'array',
            'tags' => 'array',
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
     * Get tags array for the current or requested locale.
     *
     * @return array<int, string>
     */
    public function getTags(?string $locale = null): array
    {
        $locale = Locale::content($locale);
        $tags = $this->tags;

        if (empty($tags) || ! is_array($tags)) {
            return [];
        }

        // If stored per locale: ['en' => [...], 'id' => [...]]
        if (isset($tags[$locale]) && is_array($tags[$locale])) {
            return array_values(array_filter($tags[$locale]));
        }

        // If fallback to en or id
        if (isset($tags['en']) && is_array($tags['en'])) {
            return array_values(array_filter($tags['en']));
        }

        // Flat array of strings
        if (array_is_list($tags)) {
            return array_values(array_filter($tags));
        }

        return [];
    }

    /**
     * Get CSS theme styling configurations for this pillar.
     *
     * @return array{icon_bg: string, badge_bg: string, badge_text: string, badge_border: string, card_hover: string}
     */
    public function getStyles(): array
    {
        $theme = $this->color_theme ?? 'blue';

        return self::COLOR_THEMES[$theme] ?? self::COLOR_THEMES['blue'];
    }

    /**
     * Get asset URL for uploaded icon/logo.
     */
    public function getIconUrlAttribute(): ?string
    {
        if (empty($this->icon)) {
            return null;
        }

        if (str_starts_with($this->icon, 'http://') || str_starts_with($this->icon, 'https://')) {
            return $this->icon;
        }

        $clean = ltrim($this->icon, '/');
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
