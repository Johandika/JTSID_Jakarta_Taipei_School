<?php

namespace App\Models;

use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    protected $table = 'contact_messages';

    protected $guarded = ['id'];

    public const STATUS_UNREAD = 'unread';

    public const STATUS_READ = 'read';

    public const STATUS_REPLIED = 'replied';

    public const STATUS_ARCHIVED = 'archived';

    public const SUBJECT_ADMISSIONS = 'admissions';

    public const SUBJECT_MANDARIN_PROGRAM = 'mandarin_program';

    public const SUBJECT_GENERAL = 'general';

    public const SUBJECT_ACADEMICS = 'academics';

    public const SUBJECT_OTHER = 'other';

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
     * Localized status label.
     */
    public function getStatusLabel(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        $labels = [
            self::STATUS_UNREAD => [
                'en' => 'Unread',
                'id' => 'Belum Dibaca',
                'zh-TW' => '未讀',
            ],
            self::STATUS_READ => [
                'en' => 'Read',
                'id' => 'Sudah Dibaca',
                'zh-TW' => '已讀',
            ],
            self::STATUS_REPLIED => [
                'en' => 'Replied',
                'id' => 'Sudah Dibalas',
                'zh-TW' => '已回覆',
            ],
            self::STATUS_ARCHIVED => [
                'en' => 'Archived',
                'id' => 'Diarsipkan',
                'zh-TW' => '已封存',
            ],
        ];

        return $labels[$this->status][$locale] ?? $labels[$this->status]['en'] ?? ucfirst($this->status);
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
            self::STATUS_ARCHIVED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Localized subject label.
     */
    public function getSubjectLabel(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        $subjects = [
            self::SUBJECT_ADMISSIONS => [
                'en' => 'Admissions & Enrollment',
                'id' => 'Penerimaan Siswa Baru (Admissions)',
                'zh-TW' => '入學與註冊諮詢',
            ],
            self::SUBJECT_MANDARIN_PROGRAM => [
                'en' => 'Short-Term Mandarin Program / TOCFL',
                'id' => 'Program Mandarin / TOCFL',
                'zh-TW' => '短期華語班 / TOCFL檢定',
            ],
            self::SUBJECT_GENERAL => [
                'en' => 'General Inquiry',
                'id' => 'Pertanyaan Umum',
                'zh-TW' => '一般校務諮詢',
            ],
            self::SUBJECT_ACADEMICS => [
                'en' => 'Academics & Curriculum',
                'id' => 'Akademik & Kurikulum',
                'zh-TW' => '教學與課程規劃',
            ],
            self::SUBJECT_OTHER => [
                'en' => 'Other',
                'id' => 'Lainnya',
                'zh-TW' => '其他事項',
            ],
        ];

        return $subjects[$this->subject][$locale] ?? $subjects[$this->subject]['en'] ?? ucwords(str_replace('_', ' ', $this->subject));
    }
}
