<?php

namespace App\Models;

use App\Support\Locale;
use Database\Factories\AdmissionFeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdmissionFee extends CmsModel
{
    /** @use HasFactory<AdmissionFeeFactory> */
    use HasFactory;

    protected $table = 'admission_fees';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'name' => 'array',
            'payment_period' => 'array',
            'note' => 'array',
            'file_label' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
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

        return $value[$locale] ?? $value['id'] ?? $value['en'] ?? (string) reset($value);
    }

    /**
     * Whether the fee has a stored attachment that visitors may download.
     */
    public function hasDownloadableFile(): bool
    {
        $path = (string) $this->file_path;

        return $this->is_active
            && str_starts_with($path, 'documents/')
            && ! str_contains($path, '..')
            && Storage::disk('local')->exists($path);
    }

    /**
     * File name offered to visitors: the admin's original upload name, or one derived from the fee name.
     */
    public function downloadName(): string
    {
        $extension = pathinfo((string) $this->file_path, PATHINFO_EXTENSION);
        $originalName = trim((string) preg_replace('/[\x00-\x1F\x7F"]/u', '', basename(str_replace('\\', '/', (string) $this->file_name))));

        if ($originalName !== '' && $originalName !== '.' && $originalName !== '..') {
            return strcasecmp(pathinfo($originalName, PATHINFO_EXTENSION), $extension) === 0
                ? $originalName
                : $originalName.'.'.$extension;
        }

        return (Str::slug($this->trans('name', 'en')) ?: 'admission-fee').'.'.$extension;
    }

    /**
     * Download button text in the current locale, falling back to a generic label.
     */
    public function fileLabel(?string $locale = null): string
    {
        return trim($this->trans('file_label', $locale)) ?: __('pages.admissions.fee_file_download');
    }
}
