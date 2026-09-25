<?php

namespace Database\Seeders;

use App\Models\MandarinProgram;
use Illuminate\Database\Seeder;

class MandarinProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'category' => MandarinProgram::CATEGORY_MANDARIN_CLASS,
                'title' => [
                    'en' => 'Mandarin Classes',
                    'id' => 'Mandarin Classes',
                    'zh-TW' => '華語班',
                ],
                'short_description' => [
                    'en' => 'Short-term Mandarin learning program for various levels.',
                    'id' => 'Program pembelajaran Bahasa Mandarin jangka pendek untuk berbagai tingkatan.',
                    'zh-TW' => '針對各程度學員規劃之短期華語進修課程。',
                ],
                'description' => [
                    'en' => 'Short-term Mandarin learning program with engaging methods tailored to diverse proficiency levels and practical everyday applications.',
                    'id' => 'Program pembelajaran Bahasa Mandarin jangka pendek dengan metode yang menarik dan sesuai dengan berbagai tingkat kemampuan.',
                    'zh-TW' => '採用生動多元的教學模式，依據學員不同語言程度精準分級，迅速提升聽說讀寫實用溝通力。',
                ],
                'cover_image' => null,
                'gallery_images' => [],
                'program_date_label' => [
                    'en' => 'Program Period',
                    'id' => 'Program Period',
                    'zh-TW' => '研習期間',
                ],
                'program_date_text' => [
                    'en' => 'Nov 2025 – Dec 2025',
                    'id' => 'Nov 2025 – Dec 2025',
                    'zh-TW' => '2025年11月 – 12月',
                ],
                'registration_date_text' => [
                    'en' => '09 Sep 2025 – 30 Sep 2025',
                    'id' => '09 Sep 2025 – 30 Sep 2025',
                    'zh-TW' => '2025年9月9日 – 9月30日',
                ],
                'location' => [
                    'en' => 'Jakarta Taipei School',
                    'id' => 'Jakarta Taipei School',
                    'zh-TW' => '印尼雅加達臺灣學校',
                ],
                'registration_url' => 'https://example.com/register/mandarin-classes',
                'contact_info' => [
                    'en' => 'Huayu Language Center | Phone: +62 21 8249 9946 | Email: huayu@jts.sch.id',
                    'id' => 'Huayu Language Center | Telp: +62 21 8249 9946 | Email: huayu@jts.sch.id',
                    'zh-TW' => '華語中心專線 | 電話: +62 21 8249 9946 | 電子郵件: huayu@jts.sch.id',
                ],
                'extra_info_title' => [
                    'en' => 'Available Levels',
                    'id' => 'Available Levels',
                    'zh-TW' => '開設班別與程度',
                ],
                'extra_info_items' => [
                    ['label' => 'Level 1', 'value' => 'Novice Low', 'color' => 'rose'],
                    ['label' => 'Level 2', 'value' => 'Novice High', 'color' => 'amber'],
                    ['label' => 'Level 3', 'value' => 'Intermediate Low', 'color' => 'blue'],
                    ['label' => 'Level 4', 'value' => 'Intermediate High', 'color' => 'emerald'],
                ],
                'status_override' => MandarinProgram::STATUS_REGISTRATION_OPEN,
                'show_details' => true,
                'is_active' => true,
            ],
            [
                'category' => MandarinProgram::CATEGORY_TOCFL,
                'title' => [
                    'en' => 'TOCFL Schedule Test',
                    'id' => 'TOCFL Schedule Test',
                    'zh-TW' => '華語文能力測驗 (TOCFL)',
                ],
                'short_description' => [
                    'en' => 'Chinese proficiency test for non-native speakers.',
                    'id' => 'Ujian kemahiran bahasa Mandarin standar untuk penutur non-asli.',
                    'zh-TW' => '專為非華語母語者設計之標準化華語能力檢定。',
                ],
                'description' => [
                    'en' => 'Test of Chinese as a Foreign Language (TOCFL) is the official standardized test recognized internationally for Taiwanese university admissions, scholarships, and professional certification.',
                    'id' => 'TOCFL (Test of Chinese as a Foreign Language) adalah sertifikasi resmi kemahiran bahasa Mandarin berstandar internasional yang diakui secara global untuk studi dan beasiswa ke Taiwan.',
                    'zh-TW' => '華語文能力測驗（TOCFL）為臺灣官方國家級檢定，成績廣受各國大學入學審查與獎學金甄選採認。',
                ],
                'cover_image' => null,
                'gallery_images' => [],
                'program_date_label' => [
                    'en' => 'Test Date',
                    'id' => 'Test Date',
                    'zh-TW' => '測驗日期',
                ],
                'program_date_text' => [
                    'en' => '04 Oct 2025',
                    'id' => '04 Oct 2025',
                    'zh-TW' => '2025年10月4日',
                ],
                'registration_date_text' => [
                    'en' => '09 Aug 2025 – 31 Aug 2025',
                    'id' => '09 Aug 2025 – 31 Aug 2025',
                    'zh-TW' => '2025年8月9日 – 8月31日',
                ],
                'location' => [
                    'en' => 'Jakarta Taipei School',
                    'id' => 'Jakarta Taipei School',
                    'zh-TW' => '印尼雅加達臺灣學校',
                ],
                'registration_url' => 'https://tocfl.edu.tw/OS/',
                'contact_info' => [
                    'en' => 'Official TOCFL Secretariat | Email: tocfl@jts.sch.id',
                    'id' => 'Sekretariat Resmi TOCFL | Email: tocfl@jts.sch.id',
                    'zh-TW' => 'TOCFL 考務秘書處 | 電子信箱: tocfl@jts.sch.id',
                ],
                'extra_info_title' => [
                    'en' => 'Testing Information',
                    'id' => 'Informasi Ujian',
                    'zh-TW' => '測驗資訊與規格',
                ],
                'extra_info_items' => [
                    ['label' => 'Format', 'value' => 'Computer-Based (CAT)', 'color' => 'blue'],
                    ['label' => 'Target Bands', 'value' => 'Band A, B & C', 'color' => 'emerald'],
                    ['label' => 'Certification', 'value' => 'Ministry of Education Taiwan', 'color' => 'amber'],
                ],
                'status_override' => MandarinProgram::STATUS_UPCOMING,
                'show_details' => true,
                'is_active' => true,
            ],
            [
                'category' => MandarinProgram::CATEGORY_WORKSHOP_EVENT,
                'title' => [
                    'en' => 'Chinese Culture Workshop',
                    'id' => 'Chinese Culture Workshop',
                    'zh-TW' => '中華文化體驗工作坊',
                ],
                'short_description' => [
                    'en' => 'Explore Chinese culture through fun and interactive activities.',
                    'id' => 'Jelajahi kekayaan budaya Tionghoa melalui kegiatan seru dan interaktif.',
                    'zh-TW' => '透過活潑有趣的動手實作，深入體驗中華文化之美。',
                ],
                'description' => [
                    'en' => 'Hands-on cultural enrichment workshop for students and families featuring traditional lantern making, tea art ceremonies, folk crafting, and festive arts.',
                    'id' => 'Workshop kebudayaan interaktif bagi siswa dan keluarga yang mencakup pembuatan lampion tradisional, seni teh, kerajinan tangan, dan kesenian rakyat.',
                    'zh-TW' => '專為學生及家長策劃的互動式文化工作坊，包含手作傳統燈籠、茶道品茗、剪紙藝術及民俗體驗。',
                ],
                'cover_image' => null,
                'gallery_images' => [],
                'program_date_label' => [
                    'en' => 'Program Date',
                    'id' => 'Program Date',
                    'zh-TW' => '活動日期',
                ],
                'program_date_text' => [
                    'en' => '18 Oct 2025',
                    'id' => '18 Oct 2025',
                    'zh-TW' => '2025年10月18日',
                ],
                'registration_date_text' => [
                    'en' => '01 Sep 2025 – 10 Oct 2025',
                    'id' => '01 Sep 2025 – 10 Oct 2025',
                    'zh-TW' => '2025年9月1日 – 10月10日',
                ],
                'location' => [
                    'en' => 'Jakarta Taipei School',
                    'id' => 'Jakarta Taipei School',
                    'zh-TW' => '印尼雅加達臺灣學校',
                ],
                'registration_url' => 'https://example.com/register/culture-workshop',
                'contact_info' => [
                    'en' => 'Culture Department | Email: culture@jts.sch.id',
                    'id' => 'Divisi Kebudayaan | Email: culture@jts.sch.id',
                    'zh-TW' => '文化處 | 電子信箱: culture@jts.sch.id',
                ],
                'extra_info_title' => [
                    'en' => 'Workshop Highlights',
                    'id' => 'Sorotan Kegiatan',
                    'zh-TW' => '活動亮點',
                ],
                'extra_info_items' => [
                    ['label' => 'Activity 1', 'value' => 'Traditional Lantern Crafting', 'color' => 'rose'],
                    ['label' => 'Activity 2', 'value' => 'Tea Ceremony & Tasting', 'color' => 'amber'],
                    ['label' => 'Activity 3', 'value' => 'Traditional Music & Performance', 'color' => 'emerald'],
                ],
                'status_override' => MandarinProgram::STATUS_REGISTRATION_OPEN,
                'show_details' => true,
                'is_active' => true,
            ],
            [
                'category' => MandarinProgram::CATEGORY_WORKSHOP_EVENT,
                'title' => [
                    'en' => 'Chinese Calligraphy Workshop',
                    'id' => 'Chinese Calligraphy Workshop',
                    'zh-TW' => '書法體驗課',
                ],
                'short_description' => [
                    'en' => 'Learn the basics of Chinese calligraphy and culture.',
                    'id' => 'Pelajari dasar-dasar seni kaligrafi Mandarin dan filosofi budayanya.',
                    'zh-TW' => '掌握毛筆書寫基本筆法，領略漢字結構與筆墨意境。',
                ],
                'description' => [
                    'en' => 'Master the elegant brush strokes of traditional Chinese calligraphy (Shufa) under the guidance of expert master calligraphers. Suitable for beginners.',
                    'id' => 'Kuasai teknik goresan kuas kaligrafi tradisional Mandarin bersama instruktur kaligrafi berpengalaman. Sangat cocok untuk pemula.',
                    'zh-TW' => '由資深書法名師親自示範，從執筆運腕到基本筆畫，循序漸進引領學員寫出優美漢字作品。',
                ],
                'cover_image' => null,
                'gallery_images' => [],
                'program_date_label' => [
                    'en' => 'Program Date',
                    'id' => 'Program Date',
                    'zh-TW' => '研習日期',
                ],
                'program_date_text' => [
                    'en' => '25 Nov 2025',
                    'id' => '25 Nov 2025',
                    'zh-TW' => '2025年11月25日',
                ],
                'registration_date_text' => [
                    'en' => '01 Nov 2025 – 20 Nov 2025',
                    'id' => '01 Nov 2025 – 20 Nov 2025',
                    'zh-TW' => '2025年11月1日 – 11月20日',
                ],
                'location' => [
                    'en' => 'Jakarta Taipei School',
                    'id' => 'Jakarta Taipei School',
                    'zh-TW' => '印尼雅加達臺灣學校',
                ],
                'registration_url' => 'https://example.com/register/calligraphy',
                'contact_info' => [
                    'en' => 'Art & Calligraphy Club | Email: calligraphy@jts.sch.id',
                    'id' => 'Klub Seni & Kaligrafi | Email: calligraphy@jts.sch.id',
                    'zh-TW' => '書法研習社 | 電子郵件: calligraphy@jts.sch.id',
                ],
                'extra_info_title' => [
                    'en' => 'Workshop Inclusions',
                    'id' => 'Fasilitas Workshop',
                    'zh-TW' => '課程教材與備品',
                ],
                'extra_info_items' => [
                    ['label' => 'Brushes', 'value' => 'Premium Bamboo Brushes', 'color' => 'amber'],
                    ['label' => 'Paper', 'value' => 'Handmade Xuan Paper', 'color' => 'blue'],
                    ['label' => 'Ink', 'value' => 'Traditional Pine Soot Ink', 'color' => 'slate'],
                ],
                'status_override' => MandarinProgram::STATUS_UPCOMING,
                'show_details' => true,
                'is_active' => true,
            ],
            [
                'category' => MandarinProgram::CATEGORY_TOCFL,
                'title' => [
                    'en' => 'TOCFL Schedule Test',
                    'id' => 'TOCFL Schedule Test',
                    'zh-TW' => '華語文能力測驗 (TOCFL)',
                ],
                'short_description' => [
                    'en' => 'Chinese proficiency test for non-native speakers.',
                    'id' => 'Ujian kemahiran bahasa Mandarin standar untuk penutur non-asli.',
                    'zh-TW' => '專為非華語母語者設計之標準化華語能力檢定。',
                ],
                'description' => [
                    'en' => 'The 2024 official TOCFL test examination session was successfully conducted at Jakarta Taipei School test center.',
                    'id' => 'Sesi resmi ujian TOCFL 2024 telah sukses diselenggarakan di pusat ujian Jakarta Taipei School.',
                    'zh-TW' => '2024年度臺灣教育部華語文能力測驗已於本校考場圓滿落幕。',
                ],
                'cover_image' => null,
                'gallery_images' => [],
                'program_date_label' => [
                    'en' => 'Test Date',
                    'id' => 'Test Date',
                    'zh-TW' => '測驗日期',
                ],
                'program_date_text' => [
                    'en' => '06 Dec 2024',
                    'id' => '06 Dec 2024',
                    'zh-TW' => '2024年12月6日',
                ],
                'registration_date_text' => [
                    'en' => '09 Oct 2024 – 31 Oct 2024',
                    'id' => '09 Oct 2024 – 31 Oct 2024',
                    'zh-TW' => '2024年10月9日 – 10月31日',
                ],
                'location' => [
                    'en' => 'Jakarta Taipei School',
                    'id' => 'Jakarta Taipei School',
                    'zh-TW' => '印尼雅加達臺灣學校',
                ],
                'registration_url' => null,
                'contact_info' => null,
                'extra_info_title' => null,
                'extra_info_items' => null,
                'status_override' => MandarinProgram::STATUS_PROGRAM_DONE,
                'show_details' => true,
                'is_active' => true,
            ],
            [
                'category' => MandarinProgram::CATEGORY_MANDARIN_CLASS,
                'title' => [
                    'en' => 'Spring Mandarin Class',
                    'id' => 'Spring Mandarin Class',
                    'zh-TW' => '春季華語班',
                ],
                'short_description' => [
                    'en' => 'Short-term Mandarin program for beginner level.',
                    'id' => 'Program Mandarin jangka pendek untuk tingkat pemula.',
                    'zh-TW' => '針對初學入門者規劃之春季短期華語班。',
                ],
                'description' => [
                    'en' => 'Intensive spring season Mandarin training focusing on pronunciation, practical vocabulary, daily conversation, and basic Hanzi recognition.',
                    'id' => 'Kelas intensif musim semi berfokus pada pelafalan zhuyin/pinyin, kosakata praktis, percakapan sehari-hari, serta pengenalan karakter dasar Hanzi.',
                    'zh-TW' => '春季密集華語訓練班，著重語音聲調矯正、生活實用會話及基礎常用漢字識讀。',
                ],
                'cover_image' => null,
                'gallery_images' => [],
                'program_date_label' => [
                    'en' => 'Program Period',
                    'id' => 'Program Period',
                    'zh-TW' => '研習期間',
                ],
                'program_date_text' => [
                    'en' => 'Mar 2025 – Apr 2025',
                    'id' => 'Mar 2025 – Apr 2025',
                    'zh-TW' => '2025年3月 – 4月',
                ],
                'registration_date_text' => [
                    'en' => '01 Feb 2025 – 28 Feb 2025',
                    'id' => '01 Feb 2025 – 28 Feb 2025',
                    'zh-TW' => '2025年2月1日 – 2月28日',
                ],
                'location' => [
                    'en' => 'Jakarta Taipei School',
                    'id' => 'Jakarta Taipei School',
                    'zh-TW' => '印尼雅加達臺灣學校',
                ],
                'registration_url' => null,
                'contact_info' => null,
                'extra_info_title' => null,
                'extra_info_items' => null,
                'status_override' => MandarinProgram::STATUS_PROGRAM_DONE,
                'show_details' => true,
                'is_active' => true,
            ],
            [
                'category' => MandarinProgram::CATEGORY_MANDARIN_CLASS,
                'title' => [
                    'en' => 'Business Mandarin Intensive',
                    'id' => 'Kelas Intensif Mandarin Bisnis',
                    'zh-TW' => '商務華語密集班',
                ],
                'short_description' => [
                    'en' => 'Professional Mandarin communication training for business executives and workplace settings.',
                    'id' => 'Pelatihan komunikasi Mandarin profesional untuk dunia kerja dan eksekutif bisnis.',
                    'zh-TW' => '針對職場商務溝通、經貿談判與專業簡報量身打造之實戰課程。',
                ],
                'description' => [
                    'en' => 'Structured executive curriculum focusing on commercial negotiations, cross-cultural etiquette, presentation skills, and formal correspondence.',
                    'id' => 'Kurikulum eksekutif terstruktur berfokus pada negosiasi komersial, etika bisnis lintas budaya, dan korespondensi formal.',
                    'zh-TW' => '專門強化商務接待、商用書信撰寫及經貿專業術語，協助學員拓展國際商機。',
                ],
                'cover_image' => null,
                'gallery_images' => [],
                'program_date_label' => [
                    'en' => 'Program Period',
                    'id' => 'Program Period',
                    'zh-TW' => '研習期間',
                ],
                'program_date_text' => [
                    'en' => '10 Nov 2025 – 15 Dec 2025',
                    'id' => '10 Nov 2025 – 15 Dec 2025',
                    'zh-TW' => '2025年11月10日 – 12月15日',
                ],
                'registration_date_text' => [
                    'en' => '15 Sep 2025 – 31 Oct 2025',
                    'id' => '15 Sep 2025 – 31 Oct 2025',
                    'zh-TW' => '2025年9月15日 – 10月31日',
                ],
                'location' => [
                    'en' => 'Jakarta Taipei School',
                    'id' => 'Jakarta Taipei School',
                    'zh-TW' => '印尼雅加達臺灣學校',
                ],
                'registration_url' => 'https://example.com/register/business-mandarin',
                'contact_info' => [
                    'en' => 'Executive Huayu Center | Phone: +62 21 8249 9946 | Email: executive@jts.sch.id',
                    'id' => 'Executive Huayu Center | Telp: +62 21 8249 9946 | Email: executive@jts.sch.id',
                    'zh-TW' => '商務華語推廣組 | 電話: +62 21 8249 9946 | 電子郵件: executive@jts.sch.id',
                ],
                'extra_info_title' => [
                    'en' => 'Program Focus',
                    'id' => 'Fokus Pembelajaran',
                    'zh-TW' => '核心單元',
                ],
                'extra_info_items' => [
                    ['label' => 'Module 1', 'value' => 'Commercial Meetings', 'color' => 'blue'],
                    ['label' => 'Module 2', 'value' => 'Business Etiquette', 'color' => 'amber'],
                    ['label' => 'Module 3', 'value' => 'Email & Formal Reports', 'color' => 'emerald'],
                ],
                'status_override' => MandarinProgram::STATUS_REGISTRATION_OPEN,
                'show_details' => true,
                'is_active' => true,
            ],
            [
                'category' => MandarinProgram::CATEGORY_MANDARIN_CLASS,
                'title' => [
                    'en' => "Children's Huayu Holiday Camp",
                    'id' => 'Liburan Seru Belajar Mandarin Anak',
                    'zh-TW' => '兒童華語假期營',
                ],
                'short_description' => [
                    'en' => 'Fun and interactive Mandarin learning camp for kids during school holidays.',
                    'id' => 'Camp belajar Mandarin yang seru dan interaktif untuk anak-anak selama liburan sekolah.',
                    'zh-TW' => '寓教於樂的兒童華語營隊，透過遊戲與童謠自然開口說華語。',
                ],
                'description' => [
                    'en' => 'A week-long immersive cultural and language adventure featuring interactive games, storytelling, and songs for young learners aged 6-12.',
                    'id' => 'Petualangan bahasa dan budaya selama sepekan dengan permainan interaktif, dongeng, dan lagu untuk anak usia 6-12 tahun.',
                    'zh-TW' => '專為6-12歲孩童設計，融入情境式對話與生動繪本故事，打造零壓力的全方位華語沉浸環境。',
                ],
                'cover_image' => null,
                'gallery_images' => [],
                'program_date_label' => [
                    'en' => 'Camp Period',
                    'id' => 'Periode Camp',
                    'zh-TW' => '營隊期間',
                ],
                'program_date_text' => [
                    'en' => '15 Dec 2025 – 20 Dec 2025',
                    'id' => '15 Dec 2025 – 20 Dec 2025',
                    'zh-TW' => '2025年12月15日 – 12月20日',
                ],
                'registration_date_text' => [
                    'en' => '10 Oct 2025 – 30 Nov 2025',
                    'id' => '10 Oct 2025 – 30 Nov 2025',
                    'zh-TW' => '2025年10月10日 – 11月30日',
                ],
                'location' => [
                    'en' => 'Jakarta Taipei School',
                    'id' => 'Jakarta Taipei School',
                    'zh-TW' => '印尼雅加達臺灣學校',
                ],
                'registration_url' => 'https://example.com/register/holiday-camp',
                'contact_info' => [
                    'en' => 'Junior Huayu Club | Email: junior@jts.sch.id',
                    'id' => 'Junior Huayu Club | Email: junior@jts.sch.id',
                    'zh-TW' => '幼少華語組 | 電子信箱: junior@jts.sch.id',
                ],
                'extra_info_title' => [
                    'en' => 'Age Groups',
                    'id' => 'Kelompok Usia',
                    'zh-TW' => '分齡組別',
                ],
                'extra_info_items' => [
                    ['label' => 'Group A', 'value' => 'Ages 6 - 8 (Panda)', 'color' => 'rose'],
                    ['label' => 'Group B', 'value' => 'Ages 9 - 12 (Dragon)', 'color' => 'emerald'],
                ],
                'status_override' => MandarinProgram::STATUS_UPCOMING,
                'show_details' => true,
                'is_active' => true,
            ],
            [
                'category' => MandarinProgram::CATEGORY_WORKSHOP_EVENT,
                'title' => [
                    'en' => 'Mid-Autumn Cultural Gathering',
                    'id' => 'Perayaan Festival Pertengahan Musim Gugur',
                    'zh-TW' => '中秋節文化聯歡活動',
                ],
                'short_description' => [
                    'en' => 'Traditional mooncake making, classical poetry reading, and festive riddle games.',
                    'id' => 'Pembuatan kue bulan tradisional, pembacaan puisi klasik, dan tebak teka-teki festival.',
                    'zh-TW' => '親手手作廣式月餅、朗誦中秋詩詞與猜燈謎同樂活動。',
                ],
                'description' => [
                    'en' => 'Celebration of Mid-Autumn festival fostering community harmony, traditional food culture, and literary heritage.',
                    'id' => 'Perayaan festival kue bulan mempererat kebersamaan komunitas dengan kuliner khas dan apresiasi sastra tradisional.',
                    'zh-TW' => '邀請親師生共襄盛舉，一同體驗中秋佳節傳統習俗與文化意涵。',
                ],
                'cover_image' => null,
                'gallery_images' => [],
                'program_date_label' => [
                    'en' => 'Event Date',
                    'id' => 'Tanggal Event',
                    'zh-TW' => '活動日期',
                ],
                'program_date_text' => [
                    'en' => '17 Sep 2024',
                    'id' => '17 Sep 2024',
                    'zh-TW' => '2024年9月17日',
                ],
                'registration_date_text' => [
                    'en' => '01 Sep 2024 – 15 Sep 2024',
                    'id' => '01 Sep 2024 – 15 Sep 2024',
                    'zh-TW' => '2024年9月1日 – 9月15日',
                ],
                'location' => [
                    'en' => 'Jakarta Taipei School',
                    'id' => 'Jakarta Taipei School',
                    'zh-TW' => '印尼雅加達臺灣學校',
                ],
                'registration_url' => null,
                'contact_info' => null,
                'extra_info_title' => null,
                'extra_info_items' => null,
                'status_override' => MandarinProgram::STATUS_PROGRAM_DONE,
                'show_details' => true,
                'is_active' => true,
            ],
            [
                'category' => MandarinProgram::CATEGORY_TOCFL,
                'title' => [
                    'en' => 'TOCFL Prep & Simulation Test',
                    'id' => 'Tryout & Persiapan Ujian TOCFL',
                    'zh-TW' => 'TOCFL 考前衝刺與模擬測驗',
                ],
                'short_description' => [
                    'en' => 'Comprehensive prep course and mock exam simulation for TOCFL Band A and B.',
                    'id' => 'Kelas persiapan intensif dan simulasi ujian resmi TOCFL Band A dan Band B.',
                    'zh-TW' => '針對 Band A 與 Band B 聽力及閱讀題型之精準解題技巧與模考。',
                ],
                'description' => [
                    'en' => 'Master essential test strategies, timing, and high-frequency vocabulary for passing the official TOCFL examination with confidence.',
                    'id' => 'Kuasai strategi menjawab soal, manajemen waktu, dan bank kosakata frekuensi tinggi untuk lulus ujian TOCFL dengan hasil optimal.',
                    'zh-TW' => '專業名師剖析高頻考點與常見陷阱，附贈全真模擬試題演練。',
                ],
                'cover_image' => null,
                'gallery_images' => [],
                'program_date_label' => [
                    'en' => 'Simulation Date',
                    'id' => 'Tanggal Simulasi',
                    'zh-TW' => '模擬考日期',
                ],
                'program_date_text' => [
                    'en' => '28 Oct 2025',
                    'id' => '28 Oct 2025',
                    'zh-TW' => '2025年10月28日',
                ],
                'registration_date_text' => [
                    'en' => '15 Sep 2025 – 20 Oct 2025',
                    'id' => '15 Sep 2025 – 20 Oct 2025',
                    'zh-TW' => '2025年9月15日 – 10月20日',
                ],
                'location' => [
                    'en' => 'Jakarta Taipei School Computer Lab',
                    'id' => 'Lab Komputer Jakarta Taipei School',
                    'zh-TW' => '印尼雅加達臺灣學校電腦教室',
                ],
                'registration_url' => 'https://example.com/register/tocfl-prep',
                'contact_info' => [
                    'en' => 'TOCFL Prep Center | Email: tocfl-prep@jts.sch.id',
                    'id' => 'Pusat Persiapan TOCFL | Email: tocfl-prep@jts.sch.id',
                    'zh-TW' => '檢定輔導中心 | 電子信箱: tocfl-prep@jts.sch.id',
                ],
                'extra_info_title' => [
                    'en' => 'Course Inclusions',
                    'id' => 'Fasilitas Kursus',
                    'zh-TW' => '課程包含項目',
                ],
                'extra_info_items' => [
                    ['label' => 'Mock Exam', 'value' => '2x Official Simulation', 'color' => 'blue'],
                    ['label' => 'Materials', 'value' => 'Vocabulary Handbooks', 'color' => 'amber'],
                    ['label' => 'Review', 'value' => '1-on-1 Performance Analysis', 'color' => 'emerald'],
                ],
                'status_override' => MandarinProgram::STATUS_REGISTRATION_OPEN,
                'show_details' => true,
                'is_active' => true,
            ],
        ];

        foreach ($programs as $data) {
            MandarinProgram::updateOrCreate(
                [
                    'category' => $data['category'],
                    'title->en' => $data['title']['en'],
                ],
                $data
            );
        }
    }
}
