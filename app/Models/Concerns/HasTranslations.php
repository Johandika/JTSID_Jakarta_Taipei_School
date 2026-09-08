<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

trait HasTranslations
{
    public function translations(): HasMany
    {
        return $this->hasMany('App\\Models\\'.class_basename($this).'Translation');
    }

    public function english(): HasOne
    {
        return $this->hasOne('App\\Models\\'.class_basename($this).'Translation')->where('locale', 'en')->withDefault(['locale' => 'en']);
    }

    public function chinese(): HasOne
    {
        return $this->hasOne('App\\Models\\'.class_basename($this).'Translation')->where('locale', 'zh-TW')->withDefault(['locale' => 'zh-TW']);
    }

    public function indonesian(): HasOne
    {
        return $this->hasOne('App\\Models\\'.class_basename($this).'Translation')->where('locale', 'id')->withDefault(['locale' => 'id']);
    }

    public function translation(?string $locale = null): mixed
    {
        $translations = $this->translations;

        return $translations->first(fn ($t) => $t->locale === ($locale ?? app()->getLocale()) && filled($t->title))
            ?? $translations->firstWhere('locale', 'en');
    }

    public function getTitleAttribute(): string
    {
        return $this->translation()?->title ?? $this->getRawOriginal('label') ?? $this->getRawOriginal('topic') ?? $this->slug ?? '';
    }

    public function getHtmlBodyAttribute(): string
    {
        return Str::markdown($this->translation()?->body ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->whereHas('translations', fn ($q) => $q->where('locale', 'en')->where('title', '!=', ''));
    }

    public function getTranslationStatusAttribute(): string
    {
        return collect(['en' => 'EN', 'zh-TW' => 'ZH', 'id' => 'ID'])
            ->map(fn ($label, $locale) => $label.($this->translations->contains(fn ($t) => $t->locale === $locale && filled($t->title)) ? ' OK' : ' missing'))
            ->implode(' / ');
    }
}
