<?php

namespace Tests\Feature;

use App\Filament\Resources\MandarinPrograms\MandarinProgramResource;
use App\Models\MandarinProgram;
use App\Models\User;
use Database\Seeders\MandarinProgramSeeder;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MandarinProgramTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferenceDataSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_mandarin_program_page_displays_active_programs_with_localization(): void
    {
        $this->seed(MandarinProgramSeeder::class);

        $this->get('/mandarin-program')
            ->assertOk()
            ->assertSeeText('Our Mandarin Programs')
            ->assertSeeText('Mandarin Classes')
            ->assertSeeText('TOCFL Schedule Test')
            ->assertSeeText('Chinese Culture Workshop')
            ->assertSeeText('Chinese Calligraphy Workshop');

        $this->get('/id/mandarin-program')
            ->assertOk()
            ->assertSeeText('Program Mandarin Jangka Pendek')
            ->assertSeeText('Our Mandarin Programs')
            ->assertSeeText('Mandarin Classes')
            ->assertSeeText('TOCFL Schedule Test');

        $this->get('/zh/mandarin-program')
            ->assertOk()
            ->assertSeeText('短期華語課程')
            ->assertSeeText('華語班')
            ->assertSeeText('華語文能力測驗 (TOCFL)')
            ->assertSeeText('中華文化體驗工作坊')
            ->assertSeeText('書法體驗課');
    }

    public function test_inactive_programs_are_not_displayed_on_public_page(): void
    {
        MandarinProgram::factory()->create([
            'title' => ['en' => 'Secret Hidden Program', 'id' => 'Program Rahasia Tersembunyi', 'zh-TW' => '隱藏課程'],
            'is_active' => false,
        ]);

        $this->get('/id/mandarin-program')
            ->assertOk()
            ->assertDontSeeText('Program Rahasia Tersembunyi');
    }

    public function test_completed_programs_show_program_done_treatment(): void
    {
        $this->seed(MandarinProgramSeeder::class);

        $this->get('/mandarin-program')
            ->assertOk()
            ->assertSeeText('PROGRAM DONE')
            ->assertSeeText('Program Completed');
    }

    public function test_admin_can_access_mandarin_programs_cms_resource(): void
    {
        $admin = User::where('email', 'admin@jts.local')->first();
        $this->actingAs($admin);

        $this->get(MandarinProgramResource::getUrl('index'))
            ->assertOk();

        $this->get(MandarinProgramResource::getUrl('create'))
            ->assertOk();
    }

    public function test_mandarin_program_page_renders_pagination_when_programs_exceed_nine(): void
    {
        $this->seed(MandarinProgramSeeder::class);

        // Seeder now provides 10 programs, exceeding the 3x3 (9) grid limit
        $this->get('/mandarin-program')
            ->assertOk()
            ->assertSee('role="navigation"', false)
            ->assertSeeText('results');

        $this->get('/id/mandarin-program')
            ->assertOk()
            ->assertSee('role="navigation"', false)
            ->assertSeeText('hasil');
    }

    public function test_mandarin_program_renders_default_placeholder_when_no_image_is_uploaded(): void
    {
        $program = MandarinProgram::factory()->create([
            'cover_image' => null,
            'gallery_images' => [],
            'is_active' => true,
        ]);

        $this->assertStringContainsString('placeholder.svg', $program->cover_image_url);

        $this->get('/mandarin-program')
            ->assertOk()
            ->assertSee('placeholder.svg', false);
    }

    public function test_mandarin_program_stores_multiple_gallery_images(): void
    {
        $program = MandarinProgram::factory()->create([
            'gallery_images' => [
                'mandarin-programs/sample1.webp',
                'mandarin-programs/sample2.webp',
                'mandarin-programs/sample3.webp',
            ],
        ]);

        $this->assertCount(3, $program->fresh()->gallery_images);
        $this->assertCount(3, $program->fresh()->gallery_image_urls);
        $this->assertStringContainsString('/storage/mandarin-programs/sample1.webp', $program->fresh()->gallery_image_urls[0]);
    }
}
