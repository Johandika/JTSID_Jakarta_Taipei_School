<?php

namespace Tests\Feature;

use App\Models\Facility;
use Tests\TestCase;

class FacilitiesTest extends TestCase
{
    public function test_facilities_page_renders_successfully(): void
    {
        $response = $this->get('/facilities');
        $response->assertStatus(200);

        $responseId = $this->get('/id/facilities');
        $responseId->assertStatus(200);

        $responseZh = $this->get('/zh/facilities');
        $responseZh->assertStatus(200);
    }

    public function test_facilities_shows_empty_message_when_no_records(): void
    {
        Facility::truncate();

        $response = $this->get('/id/facilities');
        $response->assertStatus(200);
        $response->assertSee(__('pages.facilities.empty'));
    }

    public function test_facilities_only_displays_tabs_for_categories_with_records(): void
    {
        Facility::truncate();

        // Create a facility only in 'sekolah'
        $facility = Facility::create([
            'category' => 'sekolah',
            'name' => [
                'id' => 'Laboratorium Komputer',
                'en' => 'Computer Lab',
                'zh-TW' => '電腦教室',
            ],
            'image' => 'images/sample.webp',
        ]);

        $response = $this->get('/id/facilities');
        $response->assertStatus(200);

        // 'Fasilitas Sekolah' tab should appear
        $response->assertSee('Fasilitas Sekolah');
        $response->assertSee('Laboratorium Komputer');

        // Other categories without items should NOT appear as filter buttons
        $response->assertDontSee('Taman Kanak-Kanak (TK)');
        $response->assertDontSee('Asrama Guru');
        $response->assertDontSee('Sekolah Dasar (SD)');

        // Clean up
        $facility->delete();
    }

    public function test_facility_category_labels_and_helpers(): void
    {
        $this->assertSame('Fasilitas Sekolah', Facility::getCategoryLabel('sekolah', 'id'));
        $this->assertSame('School Facilities', Facility::getCategoryLabel('sekolah', 'en'));
        $this->assertSame('校園設施', Facility::getCategoryLabel('sekolah', 'zh-TW'));

        $options = Facility::getCategoryOptions();
        $this->assertArrayHasKey('sekolah', $options);
        $this->assertArrayHasKey('asrama_guru', $options);
        $this->assertArrayHasKey('tk', $options);
        $this->assertArrayHasKey('sd', $options);
        $this->assertArrayHasKey('smp', $options);
        $this->assertArrayHasKey('sma', $options);
    }
}
