<?php

namespace Tests\Feature;

use App\Filament\Resources\Directors\DirectorResource;
use App\Models\Director;
use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReferenceDataSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_board_of_directors_page_displays_active_directors_with_localization(): void
    {
        Director::factory()->create([
            'name' => [
                'en' => 'Dr. Olivia Chen, Ph.D.',
                'id' => 'Dr. Olivia Chen, Ph.D.',
                'zh-TW' => '陳美玲 博士',
            ],
            'role' => [
                'en' => 'Chairman of the Board',
                'id' => 'Ketua Dewan Pengurus',
                'zh-TW' => '董事長',
            ],
            'photo' => 'images/directors/director-1.jpg',
            'is_active' => true,
        ]);

        $this->get('/board-of-directors')
            ->assertOk()
            ->assertSeeText('Chairman of the Board')
            ->assertSeeText('Dr. Olivia Chen, Ph.D.');

        $this->get('/id/board-of-directors')
            ->assertOk()
            ->assertSeeText('Ketua Dewan Pengurus')
            ->assertSeeText('Dr. Olivia Chen, Ph.D.');

        $this->get('/zh/board-of-directors')
            ->assertOk()
            ->assertSeeText('董事長')
            ->assertSeeText('陳美玲 博士');
    }

    public function test_inactive_directors_are_not_displayed_on_public_page(): void
    {
        Director::factory()->create([
            'name' => ['en' => 'Hidden Director', 'id' => 'Direktur Tersembunyi', 'zh-TW' => '隱藏董事'],
            'is_active' => false,
        ]);

        $this->get('/id/board-of-directors')
            ->assertOk()
            ->assertDontSeeText('Direktur Tersembunyi');
    }

    public function test_admin_can_access_directors_cms_resource(): void
    {
        $admin = User::where('email', 'admin@jts.local')->first();
        $this->actingAs($admin);

        $this->get(DirectorResource::getUrl('index'))
            ->assertOk();
    }
}
