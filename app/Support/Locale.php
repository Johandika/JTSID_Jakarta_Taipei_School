<?php

namespace App\Support;

class Locale
{
    public static function content(?string $locale = null): string
    {
        return match ($locale ?? app()->getLocale()) {
            'zh', 'zh_TW' => 'zh-TW',
            default => $locale ?? app()->getLocale(),
        };
    }
}
