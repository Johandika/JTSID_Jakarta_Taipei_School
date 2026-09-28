<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use Tests\TestCase;

class HeroSlideVideoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_renders_hero_slide_with_video_iframe(): void
    {
        HeroSlide::query()->delete();

        HeroSlide::create([
            'image' => 'images/seed-campus.jpg',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'title' => ['en' => 'Welcome to Jakarta Taipei School', 'id' => 'Selamat Datang', 'zh-TW' => '歡迎'],
            'description' => ['en' => 'Leading trilingual school', 'id' => 'Sekolah trilingual terkemuka', 'zh-TW' => '頂尖三語學校'],
            'tagline' => ['en' => 'JTS Campus', 'id' => 'Kampus JTS', 'zh-TW' => 'JTS校園'],
            'cta_text' => ['en' => 'Explore', 'id' => 'Jelajahi', 'zh-TW' => '探索'],
            'cta_url' => '/academics',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $response = $this->get('/');
        $response->assertOk();

        // Check for YouTube nocookie embed iframe with required autoplay & mute parameters
        $response->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
        $response->assertSee('autoplay=1', false);
        $response->assertSee('mute=1', false);
        $response->assertSee('loop=1', false);
        $response->assertSee('controls=0', false);
        $response->assertSee('images/seed-campus.jpg', false);
    }

    public function test_hero_slide_video_url_is_persisted(): void
    {
        $slide = HeroSlide::create([
            'image' => 'images/seed-campus.jpg',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'title' => ['en' => 'Video Slide', 'id' => 'Slide Video', 'zh-TW' => '影片輪播'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('hero_slides', [
            'id' => $slide->id,
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
        ]);

        $this->assertSame('dQw4w9WgXcQ', $slide->fresh()->getYouTubeId());
        $this->assertTrue($slide->fresh()->hasVideo());
    }

    public function test_slide_without_video_url_does_not_render_iframe(): void
    {
        HeroSlide::query()->delete();

        HeroSlide::create([
            'image' => 'images/seed-campus.jpg',
            'video_url' => null,
            'title' => ['en' => 'Image Only Slide', 'id' => 'Slide Gambar Saja', 'zh-TW' => '僅圖片輪播'],
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertDontSee('youtube-nocookie.com/embed', false);
    }

    public function test_hero_slide_can_be_created_with_video_url_only_without_image(): void
    {
        HeroSlide::query()->delete();

        $slide = HeroSlide::create([
            'image' => null,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'show_text' => false,
            'title' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('hero_slides', [
            'id' => $slide->id,
            'image' => null,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'show_text' => false,
        ]);

        $this->assertSame('https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg', $slide->getImageOrPosterUrl());

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
        $response->assertSee('https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg', false);
        $response->assertSee('x-show="slides[current].show_text"', false);
    }

    public function test_homepage_renders_direct_html5_video_without_iframe(): void
    {
        HeroSlide::query()->delete();

        HeroSlide::create([
            'image' => 'images/seed-campus.jpg',
            'video_url' => 'https://example.com/videos/campus-tour.mp4',
            'show_text' => true,
            'title' => ['en' => 'Direct Video Slide', 'id' => 'Slide Video Langsung', 'zh-TW' => '直接影片輪播'],
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('<video autoplay muted loop playsinline', false);
        $response->assertSee('https://example.com/videos/campus-tour.mp4', false);
        $response->assertDontSee('youtube-nocookie.com', false);
    }
}
