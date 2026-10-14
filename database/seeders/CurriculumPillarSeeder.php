<?php

namespace Database\Seeders;

use App\Models\CurriculumPillar;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class CurriculumPillarSeeder extends Seeder
{
    public function run(): void
    {
        // Default section header
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

        if (CurriculumPillar::count() === 0) {
            CurriculumPillar::create([
                'title' => [
                    'en' => 'Taiwan Curriculum',
                    'id' => 'Kurikulum Taiwan',
                    'zh-TW' => '臺灣課程',
                ],
                'description' => [
                    'en' => 'Learning based on Taiwan educational standards with Mandarin language enrichment and a strong academic foundation.',
                    'id' => 'Pembelajaran berbasis standar kurikulum Taiwan dengan pengayaan bahasa Mandarin dan fondasi akademik yang kuat.',
                    'zh-TW' => '依據臺灣教育部課綱標準，結合中文能力深耕與嚴謹的學術基礎，培育學生厚實的學力底蘊。',
                ],
                'tags' => [
                    'en' => ['Traditional Mandarin', 'Academic Foundation'],
                    'id' => ['Traditional Mandarin', 'Academic Foundation'],
                    'zh-TW' => ['繁體中文教學', '紮實學術基礎'],
                ],
                'color_theme' => 'red',
                'sort_order' => 1,
                'is_active' => true,
                'icon' => null,
            ]);

            CurriculumPillar::create([
                'title' => [
                    'en' => 'Indonesian Curriculum',
                    'id' => 'Kurikulum Indonesia',
                    'zh-TW' => '印尼國家課程',
                ],
                'description' => [
                    'en' => 'Indonesian, Pancasila, and Religions based on national standards through contextual and project-based learning.',
                    'id' => 'Bahasa Indonesia, PPKn, dan Agama berbasis standar nasional melalui pembelajaran kontekstual dan berbasis proyek.',
                    'zh-TW' => '落實印尼教育部課綱要求，開設印尼語、公民品德及宗教課程，深化在地文化認同與跨文化理解。',
                ],
                'tags' => [
                    'en' => ['Kurikulum Merdeka', 'National Identity'],
                    'id' => ['Kurikulum Merdeka', 'National Identity'],
                    'zh-TW' => ['獨立課綱', '國家認同'],
                ],
                'color_theme' => 'green',
                'sort_order' => 2,
                'is_active' => true,
                'icon' => null,
            ]);

            CurriculumPillar::create([
                'title' => [
                    'en' => 'English Curriculum',
                    'id' => 'Kurikulum Bahasa Inggris',
                    'zh-TW' => '英語國際課程',
                ],
                'description' => [
                    'en' => 'Oxford and Cambridge-based English programs developed across levels to prepare students for global standards.',
                    'id' => 'Program Bahasa Inggris berbasis Oxford dan Cambridge yang terstruktur lintas jenjang untuk standar global.',
                    'zh-TW' => '採用牛津與劍橋國際英語課程架構，提供分層分級教學，引領學生接軌全球頂尖升學標準。',
                ],
                'tags' => [
                    'en' => ['Cambridge', 'Global Opportunities'],
                    'id' => ['Cambridge', 'Global Opportunities'],
                    'zh-TW' => ['劍橋國際課程', '全球視野'],
                ],
                'color_theme' => 'blue',
                'sort_order' => 3,
                'is_active' => true,
                'icon' => null,
            ]);
        }
    }
}
