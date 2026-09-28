<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class HeroSlide extends CmsModel
{
    use HasFactory;

    protected $table = 'hero_slides';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'title' => 'array',
            'description' => 'array',
            'tagline' => 'array',
            'cta_text' => 'array',
            'sort_order' => 'integer',
            'show_text' => 'boolean',
        ]);
    }

    /**
     * Get the slide image URL, falling back automatically to the YouTube HQ thumbnail if no image is uploaded.
     */
    public function getImageOrPosterUrl(): ?string
    {
        $image = $this->image;
        if (! empty($image)) {
            return str_starts_with($image, 'http')
                ? $image
                : (file_exists(public_path(ltrim($image, '/'))) ? asset(ltrim($image, '/')) : asset('storage/'.ltrim($image, '/')));
        }

        $youtubeId = $this->getYouTubeId();
        if ($youtubeId) {
            return "https://img.youtube.com/vi/{$youtubeId}/maxresdefault.jpg";
        }

        return null;
    }

    /**
     * Get a localized field value for the current locale, falling back to English.
     */
    public function trans(string $field): string
    {
        $value = $this->{$field};
        if (! is_array($value)) {
            return (string) ($value ?? '');
        }

        $locale = app()->getLocale();

        return (string) ($value[$locale] ?? $value['en'] ?? (reset($value) ?: ''));
    }

    /**
     * Extract the 11-character YouTube video ID from the configured video URL.
     */
    public function getYouTubeId(): ?string
    {
        $url = trim((string) ($this->video_url ?? ''));
        if ($url === '') {
            return null;
        }

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|shorts\/|watch\?v=|watch\?.+&v=))([a-zA-Z0-9_-]{11})/i', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Generate an autoplaying, muted, looping YouTube embed URL.
     */
    public function getYouTubeEmbedUrl(): ?string
    {
        $id = $this->getYouTubeId();
        if (! $id) {
            return null;
        }

        $params = http_build_query([
            'autoplay' => 1,
            'mute' => 1,
            'controls' => 0,
            'loop' => 1,
            'playsinline' => 1,
            'rel' => 0,
            'showinfo' => 0,
            'modestbranding' => 1,
            'disablekb' => 1,
            'fs' => 0,
            'iv_load_policy' => 3,
            'enablejsapi' => 1,
        ]);

        return "https://www.youtube-nocookie.com/embed/{$id}?{$params}";
    }

    /**
     * Check if the slide has a valid video attached.
     */
    public function hasVideo(): bool
    {
        return ! empty($this->getYouTubeId());
    }
}
