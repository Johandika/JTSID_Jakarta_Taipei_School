<?php

namespace Tests\Feature;

use App\Filament\Resources\CurriculumPillars\CurriculumPillarResource;
use App\Models\CurriculumPillar;
use App\Models\SiteSetting;
use Database\Seeders\CurriculumPillarSeeder;
use Tests\TestCase;

class CurriculumPillarsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (CurriculumPillar::count() === 0) {
            $this->seed(CurriculumPillarSeeder::class);
        }
    }

    public function test_academics_page_renders_successfully_with_curriculum_pillars(): void
    {
        $responseEn = $this->get('/academics');
        $responseEn->assertStatus(200);
        $responseEn->assertSee('Taiwan Curriculum');
        $responseEn->assertSee('Indonesian Curriculum');
        $responseEn->assertSee('English Curriculum');

        $responseId = $this->get('/id/academics');
        $responseId->assertStatus(200);
        $responseId->assertSee('Kurikulum Taiwan');
        $responseId->assertSee('Kurikulum Indonesia');

        $responseZh = $this->get('/zh/academics');
        $responseZh->assertStatus(200);
        $responseZh->assertSee('臺灣課程');
        $responseZh->assertSee('印尼國家課程');
    }

    public function test_curriculum_pillar_helper_methods(): void
    {
        $pillar = CurriculumPillar::where('color_theme', 'red')->first();
        $this->assertNotNull($pillar);

        $this->assertSame('Taiwan Curriculum', $pillar->trans('title', 'en'));
        $this->assertSame('Kurikulum Taiwan', $pillar->trans('title', 'id'));
        $this->assertSame('臺灣課程', $pillar->trans('title', 'zh-TW'));

        $styles = $pillar->getStyles();
        $this->assertArrayHasKey('icon_bg', $styles);
        $this->assertArrayHasKey('badge_bg', $styles);
        $this->assertArrayHasKey('badge_text', $styles);
        $this->assertArrayHasKey('badge_border', $styles);

        $tagsEn = $pillar->getTags('en');
        $this->assertContains('Traditional Mandarin', $tagsEn);
    }

    public function test_dynamic_card_addition_reflects_in_academics_page(): void
    {
        $newPillar = CurriculumPillar::create([
            'title' => [
                'en' => 'STEAM Innovation Program',
                'id' => 'Program Inovasi STEAM',
                'zh-TW' => 'STEAM 創新課程',
            ],
            'description' => [
                'en' => 'Robotics, coding, and hands-on scientific discovery.',
                'id' => 'Robotika, koding, dan penemuan sains praktis.',
                'zh-TW' => '機器人、程式設計與實作科學探索。',
            ],
            'tags' => [
                'en' => ['Robotics', 'Coding'],
                'id' => ['Robotika', 'Koding'],
                'zh-TW' => ['機器人', '程式設計'],
            ],
            'color_theme' => 'purple',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        $responseEn = $this->get('/academics');
        $responseEn->assertStatus(200);
        $responseEn->assertSee('STEAM Innovation Program');
        $responseEn->assertSee('Robotics');

        $responseId = $this->get('/id/academics');
        $responseId->assertStatus(200);
        $responseId->assertSee('Program Inovasi STEAM');
        $responseId->assertSee('Robotika');

        // Clean up
        $newPillar->delete();
    }

    public function test_section_header_can_be_customized_via_site_setting(): void
    {
        SiteSetting::set('academic_curriculum_header', [
            'eyebrow' => [
                'en' => 'Customized Pillars Eyebrow',
                'id' => 'Label Kustom Pilar',
                'zh-TW' => '自訂支柱標籤',
            ],
            'heading' => [
                'en' => 'Custom Academic Headline',
                'id' => 'Judul Akademik Kustom',
                'zh-TW' => '自訂學術標題',
            ],
            'subheading' => [
                'en' => 'Custom academic description text.',
                'id' => 'Teks deskripsi akademik kustom.',
                'zh-TW' => '自訂學術說明文字。',
            ],
        ]);

        $responseEn = $this->get('/academics');
        $responseEn->assertStatus(200);
        $responseEn->assertSee('Customized Pillars Eyebrow');
        $responseEn->assertSee('Custom Academic Headline');

        $responseId = $this->get('/id/academics');
        $responseId->assertStatus(200);
        $responseId->assertSee('Label Kustom Pilar');
        $responseId->assertSee('Judul Akademik Kustom');

        // Reset to original
        SiteSetting::set('academic_curriculum_header', [
            'eyebrow' => [
                'en' => 'Three Curriculum Pillars',
                'id' => 'Tiga Pilar Kurikulum',
                'zh-TW' => '三大課程支柱',
            ],
            'heading' => [
                'en' => 'One Education, Three Excellences',
                'id' => 'Satu Pendidikan, Tiga Keunggulan',
                'zh-TW' => '一個教育，三大優勢',
            ],
            'subheading' => [
                'en' => 'JTS curriculum combines the strengths of Taiwan, Indonesia, and Cambridge English education.',
                'id' => 'Kurikulum JTS menggabungkan kekuatan pendidikan Taiwan, Indonesia, dan Cambridge English.',
                'zh-TW' => '雅加達臺灣學校課程融合臺灣教育、印尼國家課綱與劍橋英語三大優勢。',
            ],
        ]);
    }

    public function test_curriculum_pillar_cms_translations_in_all_three_languages(): void
    {
        // 1. Traditional Chinese (zh-TW)
        app()->setLocale('zh-TW');
        $this->assertSame('課程支柱', CurriculumPillarResource::getNavigationLabel());
        $this->assertSame('課程支柱', CurriculumPillarResource::getModelLabel());
        $this->assertSame('課程支柱', CurriculumPillarResource::getPluralModelLabel());
        $this->assertSame('編輯區塊標題', __('admin.curriculum_pillars.actions.edit_header'));
        $this->assertSame('新增課程支柱', __('admin.curriculum_pillars.actions.create'));
        $zhThemes = CurriculumPillar::getColorThemeOptions();
        $this->assertArrayHasKey('red', $zhThemes);
        $this->assertSame('紅色 (臺灣課程主題)', $zhThemes['red']);

        // 2. Indonesian (id)
        app()->setLocale('id');
        $this->assertSame('Pilar Kurikulum', CurriculumPillarResource::getNavigationLabel());
        $this->assertSame('Pilar Kurikulum', CurriculumPillarResource::getModelLabel());
        $this->assertSame('Pilar Kurikulum', CurriculumPillarResource::getPluralModelLabel());
        $this->assertSame('Edit Header Bagian', __('admin.curriculum_pillars.actions.edit_header'));
        $this->assertSame('Tambah Pilar Kurikulum', __('admin.curriculum_pillars.actions.create'));
        $idThemes = CurriculumPillar::getColorThemeOptions();
        $this->assertArrayHasKey('red', $idThemes);
        $this->assertSame('Merah (Tema Kurikulum Taiwan)', $idThemes['red']);

        // 3. English (en)
        app()->setLocale('en');
        $this->assertSame('Curriculum Pillars', CurriculumPillarResource::getNavigationLabel());
        $this->assertSame('Curriculum Pillar', CurriculumPillarResource::getModelLabel());
        $this->assertSame('Curriculum Pillars', CurriculumPillarResource::getPluralModelLabel());
        $this->assertSame('Edit Section Header', __('admin.curriculum_pillars.actions.edit_header'));
        $this->assertSame('Add Curriculum Pillar', __('admin.curriculum_pillars.actions.create'));
        $enThemes = CurriculumPillar::getColorThemeOptions();
        $this->assertArrayHasKey('red', $enThemes);
        $this->assertSame('Red (Taiwan Curriculum Theme)', $enThemes['red']);
    }
}
