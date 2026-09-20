<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ContactChannel;
use App\Models\Department;
use App\Models\GradeLevel;
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
        if (Post::exists()) {
            return;
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
                    'grade_level_id' => $grade->id, 'school_levels' => [$grade->code], 'department_id' => $department->id, 'file_path' => $file,
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

    private function sampleNotice(string $locale): string
    {
        return match ($locale) {
            'id' => '> Konten contoh untuk pengujian lokal. Informasi ini belum menjadi pengumuman resmi sekolah.',
            'zh-TW' => '> 此為本機測試用範例內容，並非學校正式公告。',
            default => '> Sample content for local testing. This is not an official school announcement.',
        };
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
