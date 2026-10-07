<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\HeroSlide;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\StaffProfile;
use App\Models\UniversityAcceptance;
use App\Models\UniversityLogo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['campus.webp', 'library.webp', 'classroom.webp', 'highlight.webp'] as $image) {
            if (file_exists(public_path('images/'.$image)) && ! Storage::disk('public')->exists('images/'.$image)) {
                Storage::disk('public')->put('images/'.$image, file_get_contents(public_path('images/'.$image)));
            }
        }
        if (HeroSlide::count() === 0) {
            $heroSlides = [
                [
                    'image' => 'images/campus.webp',
                    'title' => [
                        'en' => 'Excellence in Trilingual Education',
                        'id' => 'Keunggulan Pendidikan Tiga Bahasa',
                        'zh-TW' => '深耕三語，開創卓越教育',
                    ],
                    'description' => [
                        'en' => 'Combining Taiwan curriculum, English language mastery, and Indonesian culture to empower future-ready global leaders.',
                        'id' => 'Memadukan kurikulum Taiwan, penguasaan Bahasa Inggris, dan budaya Indonesia untuk mencetak generasi pemimpin masa depan.',
                        'zh-TW' => '結合臺灣優質課程、英語精熟學習與印尼多元文化，培育具備國際視野與未來競爭力的領袖人才。',
                    ],
                    'tagline' => [
                        'en' => 'Jakarta Taipei School',
                        'id' => 'Jakarta Taipei School',
                        'zh-TW' => '雅加達臺北學校',
                    ],
                    'cta_text' => [
                        'en' => 'Apply Now',
                        'id' => 'Daftar Sekarang',
                        'zh-TW' => '立即報名',
                    ],
                    'cta_url' => '/admissions',
                ],
                [
                    'image' => 'images/classroom.webp',
                    'title' => [
                        'en' => 'A Nurturing & Character-Driven Community',
                        'id' => 'Komunitas Belajar Berkarakter & Mengayomi',
                        'zh-TW' => '溫暖關懷、重視品格的學習園地',
                    ],
                    'description' => [
                        'en' => 'From kindergarten to senior high school, students discover their unique potential through collaborative learning and care.',
                        'id' => 'Dari jenjang TK hingga SMA, setiap siswa diajak menggali potensi terbaiknya dalam lingkungan yang suportif dan penuh perhatian.',
                        'zh-TW' => '從幼兒園至高中，引導每位學子在充滿支持與啟發的環境中探索熱情、發揮天賦潛能。',
                    ],
                    'tagline' => [
                        'en' => 'Our School',
                        'id' => 'Sekolah Kami',
                        'zh-TW' => '本校簡介',
                    ],
                    'cta_text' => [
                        'en' => 'Explore',
                        'id' => 'Jelajahi',
                        'zh-TW' => '了解更多',
                    ],
                    'cta_url' => '/school-profile',
                ],
                [
                    'image' => 'images/library.webp',
                    'title' => [
                        'en' => 'World-Class Pathways & Opportunities',
                        'id' => 'Peluang & Prestasi Berstandar Global',
                        'zh-TW' => '放眼世界，接軌國際頂尖學府',
                    ],
                    'description' => [
                        'en' => 'Guiding students toward prestigious universities in Taiwan and across the globe with holistic academic preparation.',
                        'id' => 'Mempersiapkan siswa meraih pintu masuk ke berbagai universitas terkemuka di Taiwan dan mancanegara dengan bekal akademik komprehensif.',
                        'zh-TW' => '全方位升學輔導與多元學習歷程，協助畢業生順利進入臺灣及全球知名頂尖大學。',
                    ],
                    'tagline' => [
                        'en' => 'Academics',
                        'id' => 'Akademik',
                        'zh-TW' => '學術課程',
                    ],
                    'cta_text' => [
                        'en' => 'Read More',
                        'id' => 'Selengkapnya',
                        'zh-TW' => '閱讀更多',
                    ],
                    'cta_url' => '/academics',
                ],
            ];
            foreach ($heroSlides as $i => $item) {
                HeroSlide::create(array_merge($item, [
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ]));
            }
        }
        $headlines = [
            ['A new chapter of learning begins', 'Babak baru pembelajaran dimulai', '迎接學習的新篇章', 'news', 'campus.webp'],
            ['Small discoveries, big possibilities', 'Penemuan kecil, peluang besar', '小小探索，無限可能', 'activity', 'library.webp'],
            ['Connecting cultures through language', 'Menghubungkan budaya melalui bahasa', '透過語言連結文化', 'exchange', 'classroom.webp'],
            ['An invitation to explore our campus', 'Undangan untuk mengenal kampus', '邀請您認識我們的校園', 'admission_notice', 'campus.webp'],
            ['Learning together in the library', 'Belajar bersama di perpustakaan', '在圖書館共同學習', 'activity', 'library.webp'],
            ['This month in our school community', 'Bulan ini di komunitas sekolah', '本月校園生活', 'newsletter', 'classroom.webp'],
        ];
        $department = Department::where('code', 'academic')->firstOrFail();
        $other = Department::where('code', 'student_affairs')->firstOrFail();
        $file = $this->sampleDocument();
        if (! Post::exists()) {
            for ($i = 0; $i < 14; $i++) {
                $h = $headlines[$i % count($headlines)];
                $post = Post::create(['slug' => 'campus-story-'.($i + 1), 'topic' => $h[0].($i >= 6 ? ' #'.($i + 1) : ''),
                    'category_id' => PostCategory::where('code', $h[3])->value('id'), 'department_id' => $i % 2 ? $other->id : $department->id,
                    'image' => 'images/'.$h[4], 'file_path' => $i % 3 === 0 ? $file : null,
                    'status' => $i === 13 ? 'draft' : 'published', 'upload_date' => today()->subDays($i * 2), 'published_at' => now()->subDays($i * 2 + 1)]);
                foreach (['en', 'id', 'zh-TW'] as $j => $locale) {
                    $post->translations()->create(['locale' => $locale, 'title' => $h[$j].($i >= 6 ? ' #'.($i + 1) : ''), 'summary' => match ($locale) {
                        'id' => 'Ruang untuk rasa ingin tahu, pengalaman baru, dan kebersamaan dalam komunitas sekolah kami.',
                        'zh-TW' => '在學校社群中，培養好奇心、開拓新體驗，一起成長。',
                        default => 'Making room for curiosity, new experiences, and shared moments in our school community.',
                    }, 'body' => match ($locale) {
                        'id' => "## Belajar dan bertumbuh bersama\n\nSetiap hari menghadirkan kesempatan untuk bertanya, berkreasi, dan berbagi. Komunitas sekolah mendukung siswa untuk menjelajahi ide melalui kegiatan belajar dan interaksi lintas budaya.\n\nHubungi sekolah untuk informasi kegiatan dan jadwal yang telah dikonfirmasi.",
                        'zh-TW' => "## 共同學習與成長\n\n每一天都是提問、創作與分享的機會。學校社群支持學生透過學習活動與跨文化互動探索新觀點。\n\n如需已確認的活動資訊及時間，請聯絡學校。",
                        default => "## Learning and growing together\n\nEvery day brings opportunities to ask questions, create, and share. Our school community supports students as they explore ideas through classroom experiences and cross-cultural connections.\n\nContact the school for confirmed activity details and schedules.",
                    }]);
                }
            }
        }
        $this->call(SubjectResourceSeeder::class);
        foreach (Department::all() as $d) {
            StaffProfile::updateOrCreate(
                ['department_id' => $d->id, 'name' => 'Sample Staff '.$d->id],
                ['title' => 'Department Coordinator']
            );
        }
        foreach (['National Taiwan University', 'National Cheng Kung University', 'Universitas Indonesia', 'National Tsing Hua University'] as $i => $name) {
            UniversityAcceptance::updateOrCreate(
                ['student_name' => 'Sample Student '.($i + 1), 'university_name' => $name],
                ['department_id' => $department->id, 'academic_year_id' => AcademicYear::latest('id')->value('id'),
                    'program_name' => 'Sample program', 'country' => $i === 2 ? 'Indonesia' : 'Taiwan']
            );
        }

        $uniLogos = [
            ['logo' => 'university-logos/ucl.svg', 'title' => 'UCL (University College London)'],
            ['logo' => 'university-logos/oxford.svg', 'title' => 'University of Oxford'],
            ['logo' => 'university-logos/lse.svg', 'title' => 'London School of Economics (LSE)'],
            ['logo' => 'university-logos/berkeley.svg', 'title' => 'University of California, Berkeley'],
            ['logo' => 'university-logos/washington.svg', 'title' => 'University of Washington'],
            ['logo' => 'university-logos/yale.svg', 'title' => 'Yale University'],
            ['logo' => 'university-logos/harvard.svg', 'title' => 'Harvard University'],
            ['logo' => 'university-logos/stanford.svg', 'title' => 'Stanford University'],
            ['logo' => 'university-logos/korea.svg', 'title' => 'Korea University'],
            ['logo' => 'university-logos/sydney.svg', 'title' => 'The University of Sydney'],
            ['logo' => 'university-logos/ntu.svg', 'title' => 'National Taiwan University (NTU)'],
        ];
        foreach ($uniLogos as $i => $item) {
            UniversityLogo::updateOrCreate(
                ['logo' => $item['logo']],
                [
                    'title' => $item['title'],
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ]
            );
        }
    }

    public function sampleDocument(): string
    {
        $stream = 'BT /F1 20 Tf 60 740 Td (Jakarta Taipei School) Tj 0 -40 Td /F1 12 Tf (SAMPLE DOCUMENT - LOCAL TESTING ONLY) Tj 0 -30 Td (Replace this file with an approved school document.) Tj ET';
        $objects = ['<< /Type /Catalog /Pages 2 0 R >>', '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>', '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream"];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $i => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n".$object."\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        }
        $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";
        Storage::disk('local')->put('documents/sample-school-document.pdf', $pdf);

        return 'documents/sample-school-document.pdf';
    }
}
