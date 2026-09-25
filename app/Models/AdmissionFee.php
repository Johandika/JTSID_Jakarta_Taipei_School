<?php

namespace App\Models;

use Database\Factories\AdmissionFeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        $locale = $locale ?? app()->getLocale();
        $value = $this->{$field};

        if (! is_array($value)) {
            return (string) ($value ?? '');
        }

        return $value[$locale] ?? $value['id'] ?? $value['en'] ?? (string) reset($value);
    }
}
