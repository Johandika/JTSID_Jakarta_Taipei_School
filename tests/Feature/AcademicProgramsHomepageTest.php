<?php

namespace Tests\Feature;

use App\Filament\Resources\AcademicPrograms\AcademicProgramResource;
use App\Models\AcademicProgram;
use App\Models\SiteSetting;
use Database\Seeders\AcademicProgramSeeder;
use Tests\TestCase;

class AcademicProgramsHomepageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (AcademicProgram::count() === 0) {
            $this->seed(AcademicProgramSeeder::class);
        }
    }

    public function test_homepage_renders_successfully_with_academic_programs(): void
    {
        $responseEn = $this->get('/');
        $responseEn->assertStatus(200);
        $responseEn->assertSee('Integrated Curriculum for Global Generations');
        $responseEn->assertSee('Kindergarten');
        $responseEn->assertSee('Primary School');

        $responseId = $this->get('/id');
        $responseId->assertStatus(200);
        $responseId->assertSee('Kurikulum Terpadu untuk Generasi Global');
        $responseId->assertSee('Pendidikan Usia Dini');

        $responseZh = $this->get('/zh');
        $responseZh->assertStatus(200);
        $responseZh->assertSee('培育全球世代的整合課程');
        $responseZh->assertSee('幼兒啟蒙教育');
    }

    public function test_academic_program_model_helpers(): void
    {
        $program = AcademicProgram::where('sort_order', 1)->first();
        $this->assertNotNull($program);

        $this->assertSame('Kindergarten', $program->trans('title', 'en'));
        $this->assertSame('Early Childhood Education', $program->trans('label', 'en'));
        $this->assertSame('幼兒啟蒙教育', $program->trans('label', 'zh-TW'));
        $this->assertNotNull($program->image_url);
        $this->assertStringContainsString('images/programs/kindergarten.webp', $program->image_url);
    }

    public function test_dynamic_program_addition_reflects_in_homepage(): void
    {
        $newProgram = AcademicProgram::create([
            'image' => 'images/programs/primary.webp',
            'label' => [
                'en' => 'SENIOR HIGH EDUCATION',
                'id' => 'PENDIDIKAN SMA',
                'zh-TW' => '高中教育階段',
            ],
            'title' => [
                'en' => 'Senior High School',
                'id' => 'Sekolah Menengah Atas',
                'zh-TW' => '高中部 Senior High',
            ],
            'description' => [
                'en' => 'College preparation, advanced electives, and career exploration.',
                'id' => 'Persiapan universitas, mata pelajaran pilihan lanjutan, dan eksplorasi karir.',
                'zh-TW' => '升學輔導、多元選修學科以及職涯自我探索。',
            ],
            'url' => '/academics',
            'sort_order' => 5,
            'is_active' => true,
        ]);

        $responseEn = $this->get('/');
        $responseEn->assertStatus(200);
        $responseEn->assertSee('Senior High School');
        $responseEn->assertSee('SENIOR HIGH EDUCATION');

        $responseId = $this->get('/id');
        $responseId->assertStatus(200);
        $responseId->assertSee('Sekolah Menengah Atas');
        $responseId->assertSee('PENDIDIKAN SMA');

        $newProgram->delete();
    }

    public function test_homepage_academics_header_can_be_customized_via_site_setting(): void
    {
        $original = SiteSetting::get('home_academics_header');

        SiteSetting::set('home_academics_header', [
            'eyebrow' => [
                'en' => 'Featured Pathways',
                'id' => 'Jalur Unggulan',
                'zh-TW' => '精選學程',
            ],
            'heading' => [
                'en' => 'Customized Academic Headline',
                'id' => 'Judul Akademik Kustom Beranda',
                'zh-TW' => '自訂首頁學程主標題',
            ],
            'subheading' => [
                'en' => 'Customized academic subheading description.',
                'id' => 'Deskripsi kustom subjudul akademik beranda.',
                'zh-TW' => '自訂首頁學程副標題說明。',
            ],
            'cta_label' => [
                'en' => 'Discover Pathways',
                'id' => 'Temukan Jalur',
                'zh-TW' => '探索學程體系',
            ],
        ]);

        $responseEn = $this->get('/');
        $responseEn->assertStatus(200);
        $responseEn->assertSee('Featured Pathways');
        $responseEn->assertSee('Customized Academic Headline');
        $responseEn->assertSee('Discover Pathways');

        $responseId = $this->get('/id');
        $responseId->assertStatus(200);
        $responseId->assertSee('Jalur Unggulan');
        $responseId->assertSee('Judul Akademik Kustom Beranda');
        $responseId->assertSee('Temukan Jalur');

        // Restore original
        if ($original) {
            SiteSetting::set('home_academics_header', $original);
        }
    }

    public function test_academic_program_cms_translations_in_all_three_languages(): void
    {
        // 1. Traditional Chinese (zh-TW)
        app()->setLocale('zh-TW');
        $this->assertSame('首頁學程', AcademicProgramResource::getNavigationLabel());
        $this->assertSame('首頁學程', AcademicProgramResource::getModelLabel());
        $this->assertSame('首頁學程', AcademicProgramResource::getPluralModelLabel());
        $this->assertSame('編輯區塊標題', __('admin.academic_programs.actions.edit_header'));
        $this->assertSame('新增學程', __('admin.academic_programs.actions.create'));

        // 2. Indonesian (id)
        app()->setLocale('id');
        $this->assertSame('Program Akademik Beranda', AcademicProgramResource::getNavigationLabel());
        $this->assertSame('Program Beranda', AcademicProgramResource::getModelLabel());
        $this->assertSame('Program Akademik Beranda', AcademicProgramResource::getPluralModelLabel());
        $this->assertSame('Edit Header Bagian', __('admin.academic_programs.actions.edit_header'));
        $this->assertSame('Tambah Program', __('admin.academic_programs.actions.create'));

        // 3. English (en)
        app()->setLocale('en');
        $this->assertSame('Homepage Programs', AcademicProgramResource::getNavigationLabel());
        $this->assertSame('Homepage Program', AcademicProgramResource::getModelLabel());
        $this->assertSame('Homepage Programs', AcademicProgramResource::getPluralModelLabel());
        $this->assertSame('Edit Section Header', __('admin.academic_programs.actions.edit_header'));
        $this->assertSame('Add Program', __('admin.academic_programs.actions.create'));
    }
}
