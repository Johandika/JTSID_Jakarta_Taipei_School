<?php

namespace Tests\Unit;

use App\Models\HeroSlide;
use App\Support\Locale;
use Tests\TestCase;

class LocaleContentTest extends TestCase
{
    public function test_content_locale_normalizes_laravel_chinese_locale(): void
    {
        $this->assertSame('zh-TW', Locale::content('zh_TW'));
        $this->assertSame('zh-TW', Locale::content('zh'));
        $this->assertSame('id', Locale::content('id'));
    }

    public function test_translatable_model_reads_traditional_chinese_content_when_app_locale_uses_underscore(): void
    {
        app()->setLocale('zh_TW');

        $slide = new HeroSlide;
        $slide->title = [
            'id' => 'Judul Indonesia',
            'en' => 'English Title',
            'zh-TW' => '繁體中文標題',
        ];

        $this->assertSame('繁體中文標題', $slide->trans('title'));
    }
}
