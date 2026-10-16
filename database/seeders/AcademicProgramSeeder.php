<?php

namespace Database\Seeders;

use App\Models\AcademicProgram;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class AcademicProgramSeeder extends Seeder
{
    public function run(): void
    {
        // Default section header for homepage academics bento section
        SiteSetting::set('home_academics_header', [
            'eyebrow' => [
                'en' => 'Academics',
                'id' => 'Akademik',
                'zh-TW' => '學術課程',
            ],
            'heading' => [
                'en' => 'Integrated Curriculum for Global Generations',
                'id' => 'Kurikulum Terpadu untuk Generasi Global',
                'zh-TW' => '培育全球世代的整合課程',
            ],
            'subheading' => [
                'en' => 'Combining the Taiwan Curriculum, multilingual education, and global perspectives to empower the development of every student.',
                'id' => 'Menggabungkan Kurikulum Taiwan, pembelajaran multibahasa, dan wawasan internasional untuk mendukung perkembangan setiap siswa.',
                'zh-TW' => '結合臺灣課程體系、多語教學與國際視野，全面支持每一位學生的多元潛能發展。',
            ],
            'cta_label' => [
                'en' => 'Explore Curriculum',
                'id' => 'Pelajari Kurikulum',
                'zh-TW' => '了解課程體系',
            ],
        ]);

        if (AcademicProgram::count() === 0) {
            AcademicProgram::create([
                'image' => 'images/programs/kindergarten.webp',
                'label' => [
                    'en' => 'Early Childhood Education',
                    'id' => 'Pendidikan Usia Dini',
                    'zh-TW' => '幼兒啟蒙教育',
                ],
                'title' => [
                    'en' => 'Kindergarten',
                    'id' => 'Kindergarten',
                    'zh-TW' => '幼兒園 Kindergarten',
                ],
                'description' => [
                    'en' => 'Joyful learning in a safe, warm, and diverse environment to nurture unique character and healthy growth for every child.',
                    'id' => 'Pembelajaran yang menyenangkan dalam lingkungan aman, hangat, dan beragam untuk mendukung karakter unik serta pertumbuhan sehat setiap anak.',
                    'zh-TW' => '在安全、溫馨且多元的環境中快樂學習，啟發獨特品格與奠定身心健康成長基礎。',
                ],
                'url' => '/academics',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            AcademicProgram::create([
                'image' => 'images/programs/primary.webp',
                'label' => [
                    'en' => 'Taiwan Curriculum',
                    'id' => 'Kurikulum Taiwan',
                    'zh-TW' => '臺灣課程體系',
                ],
                'title' => [
                    'en' => 'Primary School',
                    'id' => 'Sekolah Dasar',
                    'zh-TW' => '小學部 Primary School',
                ],
                'description' => [
                    'en' => 'Comprehensive subject learning taught in Traditional Chinese to strengthen students\' academic and communicative capabilities.',
                    'id' => 'Pembelajaran berbagai mata pelajaran dalam bahasa Mandarin Tradisional untuk memperkuat kemampuan akademik dan komunikasi siswa.',
                    'zh-TW' => '以繁體中文深入學習各學科領域，扎根學術基本功與扎實雙向溝通素養。',
                ],
                'url' => '/academics',
                'sort_order' => 2,
                'is_active' => true,
            ]);

            AcademicProgram::create([
                'image' => 'images/programs/secondary.webp',
                'label' => [
                    'en' => 'Taiwan & International Curriculum',
                    'id' => 'Kurikulum Taiwan & Internasional',
                    'zh-TW' => '臺灣暨國際雙軌課程',
                ],
                'title' => [
                    'en' => 'Secondary School',
                    'id' => 'Sekolah Menengah',
                    'zh-TW' => '中學部 Secondary School',
                ],
                'description' => [
                    'en' => 'Taiwan curriculum enriched with Indonesian, English, elective courses, and international exchange programs.',
                    'id' => 'Kurikulum Taiwan yang diperkaya Bahasa Indonesia, Bahasa Inggris, mata pelajaran pilihan, dan kegiatan pertukaran internasional.',
                    'zh-TW' => '兼融臺灣升學體系與印尼語、英語深度學習，搭配選修學科與國際交流活動。',
                ],
                'url' => '/academics',
                'sort_order' => 3,
                'is_active' => true,
            ]);

            AcademicProgram::create([
                'image' => 'images/programs/english.webp',
                'label' => [
                    'en' => 'Primary, Secondary & High School',
                    'id' => 'Primary, Secondary & High School',
                    'zh-TW' => 'Primary, Secondary & High School',
                ],
                'title' => [
                    'en' => 'English Program',
                    'id' => 'Program Bahasa Inggris',
                    'zh-TW' => '英語學程 English Program',
                ],
                'description' => [
                    'en' => 'Oxford and Cambridge-based programs preparing students for KET, PET, FCE, IELTS, and international higher education.',
                    'id' => 'Program berbasis Oxford dan Cambridge yang mempersiapkan siswa menghadapi KET, PET, FCE, IELTS, dan pendidikan tinggi internasional.',
                    'zh-TW' => '採用牛津與劍橋權威課程，引導學生穩健考取 KET、PET、FCE、IELTS 並銜接國際頂尖高等教育。',
                ],
                'url' => '/academics',
                'sort_order' => 4,
                'is_active' => true,
            ]);
        }
    }
}
