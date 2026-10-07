<?php

namespace App\Models;

use App\Support\Locale;
use Database\Factories\MandarinProgramFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MandarinProgram extends CmsModel
{
    /** @use HasFactory<MandarinProgramFactory> */
    use HasFactory;

    protected $table = 'mandarin_programs';

    public const CATEGORY_MANDARIN_CLASS = 'mandarin_class';

    public const CATEGORY_TOCFL = 'tocfl';

    public const CATEGORY_WORKSHOP_EVENT = 'workshop_event';

    public const STATUS_AUTO = 'auto';

    public const STATUS_UPCOMING = 'upcoming';

    public const STATUS_REGISTRATION_OPEN = 'registration_open';

    public const STATUS_REGISTRATION_CLOSED = 'registration_closed';

    public const STATUS_PROGRAM_DONE = 'program_done';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'title' => 'array',
            'short_description' => 'array',
            'description' => 'array',
            'gallery_images' => 'array',
            'program_date_label' => 'array',
            'program_date_text' => 'array',
            'registration_date_text' => 'array',
            'location' => 'array',
            'contact_info' => 'array',
            'extra_info_title' => 'array',
            'extra_info_items' => 'array',
            'program_start_date' => 'date',
            'program_end_date' => 'date',
            'registration_start_date' => 'date',
            'registration_end_date' => 'date',
            'show_details' => 'boolean',
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
     * Calculate or retrieve the effective status.
     */
    public function getComputedStatusAttribute(): string
    {
        if ($this->status_override && $this->status_override !== self::STATUS_AUTO) {
            return $this->status_override;
        }

        $today = now()->startOfDay();

        if ($this->program_end_date && $this->program_end_date->startOfDay()->lt($today)) {
            return self::STATUS_PROGRAM_DONE;
        }

        if (! $this->program_end_date && $this->program_start_date && $this->program_start_date->startOfDay()->lt($today)) {
            return self::STATUS_PROGRAM_DONE;
        }

        if ($this->registration_end_date && $this->registration_end_date->startOfDay()->lt($today)) {
            if ($this->program_start_date && $this->program_start_date->startOfDay()->gte($today)) {
                return self::STATUS_REGISTRATION_CLOSED;
            }

            return self::STATUS_PROGRAM_DONE;
        }

        if ($this->registration_start_date && $today->lt($this->registration_start_date->startOfDay())) {
            return self::STATUS_UPCOMING;
        }

        if ($this->registration_start_date && $today->gte($this->registration_start_date->startOfDay())) {
            if (! $this->registration_end_date || $today->lte($this->registration_end_date->startOfDay())) {
                return self::STATUS_REGISTRATION_OPEN;
            }
        }

        if ($this->program_start_date && $today->lt($this->program_start_date->startOfDay())) {
            return self::STATUS_UPCOMING;
        }

        return self::STATUS_REGISTRATION_OPEN;
    }

    /**
     * Get localized status badge label.
     */
    public function getStatusLabel(?string $status = null, ?string $locale = null): string
    {
        $status = $status ?? $this->computed_status;
        $locale = Locale::content($locale);

        $labels = [
            self::STATUS_UPCOMING => [
                'en' => 'Upcoming',
                'id' => 'Upcoming',
                'zh-TW' => '即將開放',
            ],
            self::STATUS_REGISTRATION_OPEN => [
                'en' => 'Registration Open',
                'id' => 'Registration Open',
                'zh-TW' => '報名中',
            ],
            self::STATUS_REGISTRATION_CLOSED => [
                'en' => 'Registration Closed',
                'id' => 'Registration Closed',
                'zh-TW' => '報名截止',
            ],
            self::STATUS_PROGRAM_DONE => [
                'en' => 'Program Done',
                'id' => 'Program Done',
                'zh-TW' => '已結束',
            ],
        ];

        return $labels[$status][$locale] ?? $labels[$status]['en'] ?? ucfirst($status);
    }

    /**
     * Get localized category label.
     */
    public function getCategoryLabel(?string $locale = null): string
    {
        $locale = Locale::content($locale);

        $labels = [
            self::CATEGORY_MANDARIN_CLASS => [
                'en' => 'Mandarin Class',
                'id' => 'Mandarin Class',
                'zh-TW' => '華語班',
            ],
            self::CATEGORY_TOCFL => [
                'en' => 'TOCFL',
                'id' => 'TOCFL',
                'zh-TW' => 'TOCFL',
            ],
            self::CATEGORY_WORKSHOP_EVENT => [
                'en' => 'Workshop / Event',
                'id' => 'Workshop / Event',
                'zh-TW' => '工作坊 / 活動',
            ],
        ];

        return $labels[$this->category][$locale] ?? $labels[$this->category]['en'] ?? ucwords(str_replace('_', ' ', $this->category));
    }

    /**
     * Cover image asset URL.
     */
    public function getCoverImageUrlAttribute(): string
    {
        if (! empty($this->cover_image)) {
            if (str_starts_with($this->cover_image, 'http')) {
                return $this->cover_image;
            }
            if (str_starts_with($this->cover_image, 'storage/')) {
                return asset($this->cover_image);
            }
            if (str_starts_with($this->cover_image, 'images/')) {
                return asset($this->cover_image);
            }

            return asset('storage/'.$this->cover_image);
        }

        return asset('images/placeholder.svg');
    }

    /**
     * Determine if a custom cover image has been uploaded.
     */
    public function hasCoverImage(): bool
    {
        return ! empty($this->cover_image);
    }

    /**
     * Gallery image asset URLs.
     *
     * @return array<int, string>
     */
    public function getGalleryImageUrlsAttribute(): array
    {
        if (empty($this->gallery_images) || ! is_array($this->gallery_images)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($img) {
            if (empty($img)) {
                return null;
            }
            if (str_starts_with($img, 'http')) {
                return $img;
            }
            if (str_starts_with($img, 'storage/')) {
                return asset($img);
            }
            if (str_starts_with($img, 'images/')) {
                return asset($img);
            }

            return asset('storage/'.$img);
        }, $this->gallery_images)));
    }

    /**
     * Formatted label for program date (e.g. Program Period vs Test Date vs Program Date).
     */
    public function getProgramDateLabelText(?string $locale = null): string
    {
        $customLabel = $this->trans('program_date_label', $locale);
        if (! empty($customLabel)) {
            return $customLabel;
        }

        if ($this->category === self::CATEGORY_TOCFL) {
            return 'Test Date';
        }

        if ($this->program_end_date && $this->program_start_date && $this->program_start_date->ne($this->program_end_date)) {
            return 'Program Period';
        }

        return 'Program Date';
    }

    /**
     * Formatted program date / period string.
     */
    public function getFormattedProgramPeriod(?string $locale = null): string
    {
        $customText = $this->trans('program_date_text', $locale);
        if (! empty($customText)) {
            return $customText;
        }

        if ($this->program_start_date && $this->program_end_date) {
            if ($this->program_start_date->eq($this->program_end_date)) {
                return $this->program_start_date->format('d M Y');
            }

            return $this->program_start_date->format('d M Y').' - '.$this->program_end_date->format('d M Y');
        }

        if ($this->program_start_date) {
            return $this->program_start_date->format('d M Y');
        }

        return '';
    }

    /**
     * Formatted registration period string.
     */
    public function getFormattedRegistrationPeriod(?string $locale = null): string
    {
        $customText = $this->trans('registration_date_text', $locale);
        if (! empty($customText)) {
            return $customText;
        }

        if ($this->registration_start_date && $this->registration_end_date) {
            return $this->registration_start_date->format('d M Y').' - '.$this->registration_end_date->format('d M Y');
        }

        if ($this->registration_start_date) {
            return 'From '.$this->registration_start_date->format('d M Y');
        }

        if ($this->registration_end_date) {
            return 'Until '.$this->registration_end_date->format('d M Y');
        }

        return '';
    }

    /**
     * Whether user can click to register now.
     */
    public function canRegister(): bool
    {
        return ! empty($this->registration_url)
            && $this->computed_status === self::STATUS_REGISTRATION_OPEN;
    }

    /**
     * Active scope.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Category scope.
     */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if (empty($category) || $category === 'all') {
            return $query;
        }

        return $query->where('category', $category);
    }
}
