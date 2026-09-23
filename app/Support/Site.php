<?php

namespace App\Support;

class Site
{
    public static function path(string $path = '/', ?string $locale = null): string
    {
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }
        if (! str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return '#';
        }
        $prefix = match ($locale ?? app()->getLocale()) {
            'id' => '/id', 'zh-TW' => '/zh', default => ''
        };

        return $prefix.($path === '/' ? ($prefix ? '' : '/') : $path);
    }

    public static function switchLocale(string $locale): string
    {
        $path = '/'.ltrim(preg_replace('~^/(zh|id)(/|$)~', '/', request()->getPathInfo()), '/');

        return self::path($path, $locale);
    }

    public static function image(?string $path): string
    {
        return $path ? asset('storage/'.$path) : asset('images/campus.jpg');
    }
}
