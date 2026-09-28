<?php

namespace Tests\Unit;

use App\Models\HeroSlide;
use PHPUnit\Framework\TestCase;

class HeroSlideTest extends TestCase
{
    public function test_extracts_youtube_id_from_various_url_formats(): void
    {
        $slide = new HeroSlide;

        // Standard watch URL
        $slide->video_url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $this->assertSame('dQw4w9WgXcQ', $slide->getYouTubeId());

        // Watch URL with extra query params
        $slide->video_url = 'https://www.youtube.com/watch?feature=shared&v=dQw4w9WgXcQ&t=10s';
        $this->assertSame('dQw4w9WgXcQ', $slide->getYouTubeId());

        // Short URL (youtu.be)
        $slide->video_url = 'https://youtu.be/dQw4w9WgXcQ';
        $this->assertSame('dQw4w9WgXcQ', $slide->getYouTubeId());

        // Short URL with query params
        $slide->video_url = 'https://youtu.be/dQw4w9WgXcQ?si=abcdef123';
        $this->assertSame('dQw4w9WgXcQ', $slide->getYouTubeId());

        // Embed URL
        $slide->video_url = 'https://www.youtube.com/embed/dQw4w9WgXcQ';
        $this->assertSame('dQw4w9WgXcQ', $slide->getYouTubeId());

        // Shorts URL
        $slide->video_url = 'https://www.youtube.com/shorts/dQw4w9WgXcQ';
        $this->assertSame('dQw4w9WgXcQ', $slide->getYouTubeId());

        // Raw 11-char ID
        $slide->video_url = 'dQw4w9WgXcQ';
        $this->assertSame('dQw4w9WgXcQ', $slide->getYouTubeId());

        // Empty / Null / Invalid
        $slide->video_url = null;
        $this->assertNull($slide->getYouTubeId());
        $this->assertFalse($slide->hasVideo());

        $slide->video_url = '';
        $this->assertNull($slide->getYouTubeId());

        $slide->video_url = 'https://example.com/not-a-youtube-video';
        $this->assertNull($slide->getYouTubeId());
    }

    public function test_generates_youtube_embed_url_with_autoplay_mute_loop(): void
    {
        $slide = new HeroSlide;
        $slide->video_url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

        $embedUrl = $slide->getYouTubeEmbedUrl();
        $this->assertNotNull($embedUrl);
        $this->assertStringStartsWith('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?', $embedUrl);
        $this->assertStringContainsString('autoplay=1', $embedUrl);
        $this->assertStringContainsString('mute=1', $embedUrl);
        $this->assertStringContainsString('loop=1', $embedUrl);
        $this->assertStringContainsString('playlist=dQw4w9WgXcQ', $embedUrl);
        $this->assertStringContainsString('controls=0', $embedUrl);
        $this->assertTrue($slide->hasVideo());
    }
}
