<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory, RecordsActivity;

    protected $table = 'contact_messages';

    protected $guarded = ['id'];

    public const STATUS_UNREAD = 'unread';

    public const STATUS_READ = 'read';

    public const STATUS_REPLIED = 'replied';

    public const STATUS_ARCHIVED = 'archived';

    /**
     * @var list<string>
     */
    public const STATUSES = [
        self::STATUS_UNREAD,
        self::STATUS_READ,
        self::STATUS_REPLIED,
        self::STATUS_ARCHIVED,
    ];

    public const SUBJECT_ADMISSIONS = 'admissions';

    public const SUBJECT_MANDARIN_PROGRAM = 'mandarin_program';

    public const SUBJECT_GENERAL = 'general';

    public const SUBJECT_ACADEMICS = 'academics';

    public const SUBJECT_OTHER = 'other';

    /**
     * Subjects offered on the public contact form, in display order.
     *
     * @var list<string>
     */
    public const SUBJECTS = [
        self::SUBJECT_ADMISSIONS,
        self::SUBJECT_MANDARIN_PROGRAM,
        self::SUBJECT_GENERAL,
        self::SUBJECT_ACADEMICS,
        self::SUBJECT_OTHER,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Scope query to only unread messages.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_UNREAD);
    }

    /**
     * Scope query to recent messages.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Admin status options in the current locale.
     *
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return collect(self::STATUSES)
            ->mapWithKeys(fn (string $status): array => [$status => self::translatedLabel('statuses', $status)])
            ->all();
    }

    /**
     * Admin subject options in the current locale.
     *
     * @return array<string, string>
     */
    public static function subjectOptions(): array
    {
        return collect(self::SUBJECTS)
            ->mapWithKeys(fn (string $subject): array => [$subject => self::translatedLabel('subjects', $subject)])
            ->all();
    }

    /**
     * Localized status label.
     */
    public function getStatusLabel(): string
    {
        return self::translatedLabel('statuses', $this->status);
    }

    /**
     * Filament badge color for status.
     */
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            self::STATUS_UNREAD => 'warning',
            self::STATUS_READ => 'info',
            self::STATUS_REPLIED => 'success',
            default => 'gray',
        };
    }

    /**
     * Localized subject label.
     */
    public function getSubjectLabel(): string
    {
        return self::translatedLabel('subjects', $this->subject);
    }

    private static function translatedLabel(string $group, string $value): string
    {
        $key = 'admin.contact.'.$group.'.'.$value;

        return Lang::has($key) ? __($key) : ucwords(str_replace('_', ' ', $value));
    }
}
