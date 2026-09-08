<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ContactChannel;
use App\Models\Department;
use App\Models\GradeLevel;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\SlideshowItem;
use App\Models\StaffProfile;
use App\Models\SubjectResource;
use App\Models\SubjectResourceType;
use App\Models\UniversityAcceptance;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        foreach (['seed-campus.jpg', 'seed-library.jpg', 'seed-classroom.jpg', 'seed-highlight.jpg'] as $image) {
            if (! Storage::disk('public')->exists('images/'.$image)) {
                Storage::disk('public')->put('images/'.$image, file_get_contents(public_path('images/'.$image)));
            }
        }
        if (Page::exists()) {
            return;
        }
        $pages = [
            'about' => ['About Jakarta Taipei School', 'Tentang Jakarta Taipei School', '認識雅加達臺灣學校'],
            'academics' => ['Academics', 'Akademik', '課程設計'],
            'admissions' => ['Admissions', 'Pendaftaran', '招生資訊'],
            'board-of-directors' => ['Board of Directors', 'Dewan Pengurus', '理董事會'],
            'school-profile' => ['School Profile', 'Profil Sekolah', '學校概況'],
            'history' => ['Our History', 'Sejarah Sekolah', '學校歷史'],
            'parent-association' => ['Parent Association', 'Asosiasi Orang Tua', '家長委員會'],
            'contact' => ['Location & Contact', 'Lokasi & Kontak', '交通資訊與聯絡方式'],
            'mandarin-program' => ['Short-term Mandarin Program', 'Program Mandarin Jangka Pendek', '短期華語課程'],
        ];
        foreach ($pages as $slug => $titles) {
            $page = Page::create(['slug' => $slug, 'status' => 'published', 'published_at' => now()->subDay(),
                'template' => match ($slug) {
                    'academics' => 'academics', 'admissions' => 'admission', default => 'default'
                },
                'hero_image' => in_array($slug, ['about', 'admissions', 'contact']) ? 'images/seed-campus.jpg' : null,
                'gallery_url' => $slug === 'admissions' ? 'https://www.jtsid.org/infomation03.php' : null,
            ]);
            foreach (['en', 'id', 'zh-TW'] as $i => $locale) {
                $body = $this->pageBody($slug, $locale);
                $page->translations()->create(['locale' => $locale, 'title' => $titles[$i], 'summary' => match ($locale) {
                    'id' => 'Mengenal komunitas, kegiatan belajar, dan kehidupan di Jakarta Taipei School.',
                    'zh-TW' => '認識雅加達臺灣學校的社群、學習與校園生活。',
                    default => 'Discover our community, learning, and life at Jakarta Taipei School.',
                }, 'body' => $body, 'seo_title' => $titles[$i]]);
            }
        }
        $menus = [
            ['/about', 'about'], ['/academics', 'academics'], ['/admissions', 'admissions'], ['/news', 'news'], ['/resources', 'resources'], ['/university-admission', 'universities'],
        ];
        foreach ($menus as $order => [$url, $key]) {
            $menu = $this->menu($url, $key, $order);
            if ($key === 'about') {
                foreach (['board-of-directors', 'school-profile', 'history', 'parent-association'] as $childOrder => $slug) {
                    $child = MenuItem::create(['parent_id' => $menu->id, 'label' => $pages[$slug][0], 'url' => '/'.$slug, 'sort_order' => $childOrder]);
                    foreach (['en', 'id', 'zh-TW'] as $i => $locale) {
                        $child->translations()->create(['locale' => $locale, 'title' => $pages[$slug][$i]]);
                    }
                }
                $this->menu('/staff', 'staff', 5, $menu->id);
                $this->menu('/contact', 'contact', 6, $menu->id);
            }
            if ($key === 'admissions') {
                $child = MenuItem::create(['parent_id' => $menu->id, 'label' => $pages['mandarin-program'][0], 'url' => '/mandarin-program']);
                foreach (['en', 'id', 'zh-TW'] as $i => $locale) {
                    $child->translations()->create(['locale' => $locale, 'title' => $pages['mandarin-program'][$i]]);
                }
            }
        }
        foreach (['about', 'admissions', 'staff', 'contact'] as $order => $key) {
            $this->menu('/'.$key, $key, $order, null, 'footer');
        }

        $headlines = [
            ['A new chapter of learning begins', 'Babak baru pembelajaran dimulai', '迎接學習的新篇章', 'news', 'seed-campus.jpg'],
            ['Small discoveries, big possibilities', 'Penemuan kecil, peluang besar', '小小探索，無限可能', 'activity', 'seed-library.jpg'],
            ['Connecting cultures through language', 'Menghubungkan budaya melalui bahasa', '透過語言連結文化', 'exchange', 'seed-classroom.jpg'],
            ['An invitation to explore our campus', 'Undangan untuk mengenal kampus', '邀請您認識我們的校園', 'admission_notice', 'seed-campus.jpg'],
            ['Learning together in the library', 'Belajar bersama di perpustakaan', '在圖書館共同學習', 'activity', 'seed-library.jpg'],
            ['This month in our school community', 'Bulan ini di komunitas sekolah', '本月校園生活', 'newsletter', 'seed-classroom.jpg'],
        ];
        $department = Department::where('code', 'academic')->firstOrFail();
        $other = Department::where('code', 'student_affairs')->firstOrFail();
        $file = $this->sampleDocument();
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
                }, 'body' => $this->sampleNotice($locale)."\n\n".match ($locale) {
                    'id' => "## Belajar dan bertumbuh bersama\n\nSetiap hari menghadirkan kesempatan untuk bertanya, berkreasi, dan berbagi. Komunitas sekolah mendukung siswa untuk menjelajahi ide melalui kegiatan belajar dan interaksi lintas budaya.\n\nHubungi sekolah untuk informasi kegiatan dan jadwal yang telah dikonfirmasi.",
                    'zh-TW' => "## 共同學習與成長\n\n每一天都是提問、創作與分享的機會。學校社群支持學生透過學習活動與跨文化互動探索新觀點。\n\n如需已確認的活動資訊及時間，請聯絡學校。",
                    default => "## Learning and growing together\n\nEvery day brings opportunities to ask questions, create, and share. Our school community supports students as they explore ideas through classroom experiences and cross-cultural connections.\n\nContact the school for confirmed activity details and schedules.",
                }]);
            }
        }
        foreach (SubjectResourceType::all() as $type) {
            foreach (GradeLevel::all() as $grade) {
                $resource = SubjectResource::create(['topic' => $grade->name.' '.$type->name.' 2026 / 2027', 'type_id' => $type->id,
                    'grade_level_id' => $grade->id, 'department_id' => $department->id, 'file_path' => $file,
                    'upload_date' => today()->subDays($type->id), 'published_at' => now()->subDay(), 'status' => 'published']);
                foreach (['en', 'id', 'zh-TW'] as $locale) {
                    $resource->translations()->create(['locale' => $locale, 'title' => $grade->name.' '.__('site.'.$type->code, [], $locale).' 2026 / 2027', 'body' => $this->sampleNotice($locale)]);
                }
            }
        }
        SlideshowItem::create(['topic' => 'Campus life', 'image' => 'images/seed-campus.jpg', 'link_url' => '/about', 'sort_order' => 1]);
        SlideshowItem::create(['topic' => 'School highlight', 'image' => 'images/seed-highlight.jpg', 'link_url' => '/news', 'sort_order' => 2]);
        foreach (Department::all() as $d) {
            StaffProfile::create(['department_id' => $d->id, 'name' => 'Sample Staff '.$d->id, 'title' => 'Department Coordinator', 'extension' => '10'.$d->id,
                'responsibilities' => 'Sample profile for local testing. Replace with approved staff information.']);
        }
        foreach (['National Taiwan University', 'National Cheng Kung University', 'Universitas Indonesia', 'National Tsing Hua University'] as $i => $name) {
            UniversityAcceptance::create(['department_id' => $department->id, 'academic_year_id' => AcademicYear::latest('id')->value('id'),
                'student_name' => 'Sample Student '.($i + 1), 'university_name' => $name, 'program_name' => 'Sample program', 'country' => $i === 2 ? 'Indonesia' : 'Taiwan']);
        }
        foreach ([
            ['admission_tel', 'Admissions', '+62 21 452 3273', true], ['phone', 'Telephone', '+62 21 452 3273', false],
            ['address', 'Campus', 'Kelapa Gading, Jakarta Utara, Indonesia', false],
            ['whatsapp', 'WhatsApp', 'https://wa.me/6281385435909', true],
            ['instagram', 'Instagram', 'https://www.instagram.com/jakartataipeischoolofficial/', true],
        ] as $order => [$channel, $label, $value, $header]) {
            $qr = null;
            if (in_array($channel, ['whatsapp', 'instagram'])) {
                $qr = 'images/qr-'.$channel.'.png';
                $options = new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'outputBase64' => false, 'scale' => 5]);
                Storage::disk('public')->put($qr, (new QRCode($options))->render($value));
            }
            ContactChannel::create(['channel' => $channel, 'label' => $label, 'value' => $value, 'qr_image' => $qr, 'is_visible_header' => $header, 'sort_order' => $order]);
        }
    }

    private function menu(string $url, string $key, int $order, ?int $parent = null, string $area = 'header'): MenuItem
    {
        $menu = MenuItem::create(['label' => __('site.'.$key, [], 'en'), 'url' => $url, 'sort_order' => $order, 'parent_id' => $parent, 'menu_area' => $area]);
        foreach (['en', 'id', 'zh-TW'] as $locale) {
            $menu->translations()->create(['locale' => $locale, 'title' => __('site.'.$key, [], $locale)]);
        }

        return $menu;
    }

    private function sampleNotice(string $locale): string
    {
        return match ($locale) {
            'id' => '> Konten contoh untuk pengujian lokal. Informasi ini belum menjadi pengumuman resmi sekolah.',
            'zh-TW' => '> 此為本機測試用範例內容，並非學校正式公告。',
            default => '> Sample content for local testing. This is not an official school announcement.',
        };
    }

    private function pageBody(string $slug, string $locale): string
    {
        $body = match ($slug) {
            'academics' => match ($locale) {
                'id' => "## Mandarin\n\nBahasa, literasi, matematika, sains, dan pembelajaran budaya dari TK hingga SMA.\n\n## Bahasa Indonesia, Pancasila, dan Agama\n\nMengenal bahasa serta kehidupan masyarakat Indonesia melalui pembelajaran yang relevan.\n\n## Bahasa Inggris\n\nMengembangkan kepercayaan diri dalam membaca, menulis, menyimak, dan berbicara.",
                'zh-TW' => "## 華語課程\n\n從幼兒園到高中，涵蓋語文、數學、科學及文化學習。\n\n## 印尼語、建國五原則與宗教\n\n透過相關課程認識印尼語言與社會生活。\n\n## 英語課程\n\n培養學生閱讀、寫作、聆聽與口語溝通的自信。",
                default => "## Mandarin\n\nLanguage, literacy, mathematics, science, and cultural learning from kindergarten through senior high school.\n\n## Indonesian, Pancasila, and Religions\n\nConnecting with the language and life of Indonesia through locally relevant learning.\n\n## English\n\nBuilding confidence in reading, writing, listening, and speaking.",
            },
            'admissions' => match ($locale) {
                'id' => "## Mulai perjalanan Anda di JTS\n\n1. Hubungi bagian pendaftaran untuk mengetahui jenjang dan ketersediaan tempat.\n2. Jadwalkan kunjungan sekolah.\n3. Minta formulir dan daftar dokumen terbaru.\n4. Ikuti proses penerimaan yang diarahkan sekolah.\n\n## Pertanyaan umum\n\n**Bagaimana cara mengetahui biaya dan jadwal?**\n\nHubungi bagian pendaftaran untuk informasi resmi terbaru.\n\n**Apakah ada program Mandarin jangka pendek?**\n\nLihat [program Mandarin](/id/mandarin-program) dan konfirmasi ketersediaan kelas kepada sekolah.",
                'zh-TW' => "## 開啟您在本校的旅程\n\n1. 聯絡招生處，了解學制及名額。\n2. 預約校園參觀。\n3. 索取最新申請表及文件清單。\n4. 依學校指引完成入學程序。\n\n## 常見問題\n\n**如何查詢費用與時間？**\n\n請聯絡招生處取得最新正式資訊。\n\n**是否提供短期華語課程？**\n\n請參閱[華語課程](/zh/mandarin-program)，並向學校確認開班資訊。",
                default => "## Start your journey at JTS\n\n1. Contact admissions to discuss grade placement and availability.\n2. Arrange a campus visit.\n3. Request the current application form and document checklist.\n4. Follow the admissions process provided by the school.\n\n## Frequently asked questions\n\n**How can I find current fees and dates?**\n\nContact the admissions team for current, approved information.\n\n**Is there a short-term Mandarin program?**\n\nExplore the [Mandarin program](/mandarin-program) and confirm class availability with the school.",
            },
            'contact' => match ($locale) {
                'id' => "## Kunjungi Jakarta Taipei School\n\nKelapa Gading, Jakarta Utara, Indonesia.\n\nTelepon: +62 21 452 3273\n\nSilakan hubungi sekolah sebelum berkunjung. [Hubungi melalui WhatsApp](https://wa.me/6281385435909).",
                'zh-TW' => "## 造訪雅加達臺灣學校\n\n印尼雅加達北區 Kelapa Gading。\n\n電話：+62 21 452 3273\n\n參觀前請先聯絡學校。[透過 WhatsApp 聯絡](https://wa.me/6281385435909)。",
                default => "## Visit Jakarta Taipei School\n\nKelapa Gading, North Jakarta, Indonesia.\n\nTelephone: +62 21 452 3273\n\nPlease contact the school before visiting. [Contact us on WhatsApp](https://wa.me/6281385435909).",
            },
            default => match ($locale) {
                'id' => "## Komunitas sekolah kami\n\nJakarta Taipei School menghubungkan bahasa, budaya, dan pengalaman belajar dalam lingkungan sekolah yang beragam.\n\n## Belajar bersama\n\nKehidupan sekolah mencakup pembelajaran akademik, bahasa, kegiatan siswa, dan hubungan dengan orang tua.\n\nKonten rinci untuk halaman ini dapat dilengkapi dengan informasi yang telah disetujui sekolah.",
                'zh-TW' => "## 我們的學校社群\n\n雅加達臺灣學校在多元環境中連結語言、文化與學習經驗。\n\n## 共同學習\n\n校園生活涵蓋學科、語言、學生活動，以及與家長的合作。\n\n本頁詳細內容待學校核准後補充。",
                default => "## Our school community\n\nJakarta Taipei School brings together languages, cultures, and learning experiences in a diverse school environment.\n\n## Learning together\n\nSchool life includes academic learning, languages, student activities, and partnerships with parents.\n\nDetailed information for this page can be completed with school-approved content.",
            },
        };

        return $this->sampleNotice($locale)."\n\n".$body;
    }

    private function sampleDocument(): string
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
