<?php

namespace Database\Seeders;

use App\Models\Director;
use Illuminate\Database\Seeder;

class DirectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $directors = [
            [
                'photo' => 'images/directors/director-1.webp',
                'name' => [
                    'en' => 'Dr. Olivia Chen, Ph.D.',
                    'id' => 'Dr. Olivia Chen, Ph.D.',
                    'zh-TW' => '陳美玲 博士 (Dr. Olivia Chen)',
                ],
                'role' => [
                    'en' => 'Chairman of the Board',
                    'id' => 'Ketua Dewan Pengurus',
                    'zh-TW' => '董事長',
                ],
                'education' => [
                    'en' => [
                        'Ph.D. in Educational Management - National Taiwan University',
                        'Master of Arts in Curriculum Design - Melbourne University',
                    ],
                    'id' => [
                        'Ph.D. in Educational Management - National Taiwan University',
                        'Master of Arts in Curriculum Design - Melbourne University',
                    ],
                    'zh-TW' => [
                        '國立臺灣大學 教育管理學博士',
                        '澳洲墨爾本大學 課程與教學碩士',
                    ],
                ],
                'bio' => [
                    'en' => 'Over 25 years of experience leading international education institutions, committed to fostering premier trilingual curriculum excellence and holistic youth leadership.',
                    'id' => 'Berpengalaman lebih dari 25 tahun memimpin institusi pendidikan internasional dan berdedikasi penuh dalam pengembangan kurikulum trilingual unggulan berstandar global.',
                    'zh-TW' => '深耕國際教育領導逾25年，全心推動全人教育、深化三語特色課程，並致力於打造兼具在地關懷與全球視野的優質學術環境。',
                ],
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'photo' => 'images/directors/director-2.webp',
                'name' => [
                    'en' => 'Ir. John Hartono, M.B.A.',
                    'id' => 'Ir. John Hartono, M.B.A.',
                    'zh-TW' => '黃文雄 碩士 (Ir. John Hartono)',
                ],
                'role' => [
                    'en' => 'Vice Chairman',
                    'id' => 'Wakil Ketua Dewan Pengurus',
                    'zh-TW' => '副董事長',
                ],
                'education' => [
                    'en' => [
                        'M.B.A. - Wharton School of the University of Pennsylvania',
                        'B.Sc. in Industrial Engineering - National Tsing Hua University',
                    ],
                    'id' => [
                        'M.B.A. - Wharton School of the University of Pennsylvania',
                        'B.Sc. in Industrial Engineering - National Tsing Hua University',
                    ],
                    'zh-TW' => [
                        '美國賓夕法尼亞大學華頓商學院 工商管理碩士 (MBA)',
                        '國立清華大學 工業工程學士',
                    ],
                ],
                'bio' => [
                    'en' => 'Multinational corporate executive specializing in governance, strategic institutional partnerships, and building sustainable alumni-industry networks.',
                    'id' => 'Praktisi manajemen korporasi multinasional dengan fokus penguatan tata kelola institusi, kemitraan strategis, serta sinergi komunitas alumni dan industri.',
                    'zh-TW' => '跨國企業高階經營管理專家，專注於學校治理架構健全、校企產學合作及國際校友網絡之深耕發展。',
                ],
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'photo' => 'images/directors/director-3.webp',
                'name' => [
                    'en' => 'Prof. Ava Lin, Ed.D.',
                    'id' => 'Prof. Ava Lin, Ed.D.',
                    'zh-TW' => '林雅慧 教授 (Prof. Ava Lin)',
                ],
                'role' => [
                    'en' => 'Academic Affairs Supervisor',
                    'id' => 'Pengawas Komite Pendidikan',
                    'zh-TW' => '學術與教育督導',
                ],
                'education' => [
                    'en' => [
                        'Doctor of Education (Ed.D.) - Teachers College, Columbia University',
                        'B.Ed. in Secondary Education - National Taiwan Normal University',
                    ],
                    'id' => [
                        'Doctor of Education (Ed.D.) - Teachers College, Columbia University',
                        'B.Ed. in Secondary Education - National Taiwan Normal University',
                    ],
                    'zh-TW' => [
                        '美國哥倫比亞大學師範學院 教育學博士 (Ed.D.)',
                        '國立臺灣師範大學 中等教育學士',
                    ],
                ],
                'bio' => [
                    'en' => 'International pedagogy expert guiding the seamless harmony between the Taiwan curriculum, Cambridge standards, and Indonesian national educational requirements.',
                    'id' => 'Ahli pedagogi internasional yang mengawal integrasi harmonis antara silabus Taiwan, standar internasional Cambridge, serta muatan kurikulum nasional Indonesia.',
                    'zh-TW' => '國際比較教育與雙語教學權威，督導臺灣課綱核心素養、劍橋國際學程與印尼本國課程之無縫融會與教學品質。',
                ],
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'photo' => 'images/directors/director-4.webp',
                'name' => [
                    'en' => 'Peter Gordon, M.Sc., CPA',
                    'id' => 'Peter Gordon, M.Sc., CPA',
                    'zh-TW' => '高培德 總監 (Peter Gordon, CPA)',
                ],
                'role' => [
                    'en' => 'Finance & Operations Director',
                    'id' => 'Direktur Operasional & Keuangan',
                    'zh-TW' => '財務與營運總監',
                ],
                'education' => [
                    'en' => [
                        'M.Sc. in Finance - London School of Economics (LSE)',
                        'B.Econ. in Accounting - National Chengchi University',
                    ],
                    'id' => [
                        'M.Sc. in Finance - London School of Economics (LSE)',
                        'B.Econ. in Accounting - National Chengchi University',
                    ],
                    'zh-TW' => [
                        '英國倫敦政治經濟學院 (LSE) 財務金融碩士',
                        '國立政治大學 會計學士',
                    ],
                ],
                'bio' => [
                    'en' => 'Specialist in modern fiscal management and campus operations, ensuring world-class, safe, and sustainable facilities for every student.',
                    'id' => 'Spesialis tata kelola fiskal dan operasional modern yang memastikan fasilitas kampus berstandar internasional, aman, dan berkelanjutan untuk seluruh peserta didik.',
                    'zh-TW' => '現代學校財務審計與營運優化專家，確保校園設施現代化、永續發展與教學資源之精準配置。',
                ],
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($directors as $data) {
            Director::updateOrCreate(
                ['photo' => $data['photo']],
                $data
            );
        }
    }
}
