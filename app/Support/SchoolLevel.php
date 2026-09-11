<?php

namespace App\Support;

class SchoolLevel
{
    public const OPTIONS = ['tk' => 'TK', 'sd' => 'SD', 'smp' => 'SMP', 'sma' => 'SMA'];

    public static function selected(): string
    {
        $level = session('school_level', 'all');

        return is_string($level) && array_key_exists($level, self::OPTIONS) ? $level : 'all';
    }

    public static function url(string $level): string
    {
        $query = request()->except(['school_level', 'page', 'grade']);

        return request()->url().'?'.http_build_query([...$query, 'school_level' => $level]);
    }
}
