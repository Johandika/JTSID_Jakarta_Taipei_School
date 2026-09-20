<?php

$standardPage = [
    'notice' => 'Konten contoh untuk pengujian lokal. Informasi ini belum menjadi pengumuman resmi sekolah.',
    'community_heading' => 'Komunitas sekolah kami',
    'community_body' => 'Jakarta Taipei School menghubungkan bahasa, budaya, dan pengalaman belajar dalam lingkungan sekolah yang beragam.',
    'learning_heading' => 'Belajar bersama',
    'learning_body' => 'Kehidupan sekolah mencakup pembelajaran akademik, bahasa, kegiatan siswa, dan hubungan dengan orang tua.',
    'closing' => 'Konten rinci untuk halaman ini dapat dilengkapi dengan informasi yang telah disetujui sekolah.',
];

$summary = 'Mengenal komunitas, kegiatan belajar, dan kehidupan di Jakarta Taipei School.';

return [
    'about' => [
        'title' => 'Tentang Jakarta Taipei School',
        'summary' => $summary,
        'seo_title' => 'Tentang Jakarta Taipei School',
        'body' => $standardPage,
    ],
    'academics' => [
        'title' => 'Akademik',
        'summary' => $summary,
        'seo_title' => 'Akademik',
        'notice' => $standardPage['notice'],
        'mandarin_heading' => 'Mandarin',
        'mandarin_body' => 'Bahasa, literasi, matematika, sains, dan pembelajaran budaya dari TK hingga SMA.',
        'indonesian_heading' => 'Bahasa Indonesia, Pancasila, dan Agama',
        'indonesian_body' => 'Mengenal bahasa serta kehidupan masyarakat Indonesia melalui pembelajaran yang relevan.',
        'english_heading' => 'Bahasa Inggris',
        'english_body' => 'Mengembangkan kepercayaan diri dalam membaca, menulis, menyimak, dan berbicara.',
    ],
    'admissions' => [
        'title' => 'Pendaftaran',
        'summary' => $summary,
        'seo_title' => 'Pendaftaran',
        'notice' => $standardPage['notice'],
        'journey_heading' => 'Mulai perjalanan Anda di JTS',
        'steps' => [
            'Hubungi bagian pendaftaran untuk mengetahui jenjang dan ketersediaan tempat.',
            'Jadwalkan kunjungan sekolah.',
            'Minta formulir dan daftar dokumen terbaru.',
            'Ikuti proses penerimaan yang diarahkan sekolah.',
        ],
        'faq_heading' => 'Pertanyaan umum',
        'fees_question' => 'Bagaimana cara mengetahui biaya dan jadwal?',
        'fees_answer' => 'Hubungi bagian pendaftaran untuk informasi resmi terbaru.',
        'mandarin_question' => 'Apakah ada program Mandarin jangka pendek?',
        'mandarin_prefix' => 'Lihat ',
        'mandarin_link' => 'program Mandarin',
        'mandarin_suffix' => ' dan konfirmasi ketersediaan kelas kepada sekolah.',
    ],
    'board_of_directors' => [
        'title' => 'Dewan Pengurus',
        'summary' => $summary,
        'seo_title' => 'Dewan Pengurus',
        'body' => $standardPage,
    ],
    'school_profile' => [
        'title' => 'Profil Sekolah',
        'summary' => $summary,
        'seo_title' => 'Profil Sekolah',
        'body' => $standardPage,
    ],
    'history' => [
        'title' => 'Sejarah Sekolah',
        'summary' => $summary,
        'seo_title' => 'Sejarah Sekolah',
        'body' => $standardPage,
    ],
    'parent_association' => [
        'title' => 'Asosiasi Orang Tua',
        'summary' => $summary,
        'seo_title' => 'Asosiasi Orang Tua',
        'body' => $standardPage,
    ],
    'contact' => [
        'title' => 'Lokasi & Kontak',
        'summary' => $summary,
        'seo_title' => 'Lokasi & Kontak',
        'notice' => $standardPage['notice'],
        'heading' => 'Kunjungi Jakarta Taipei School',
        'address' => 'Kelapa Gading, Jakarta Utara, Indonesia.',
        'telephone' => 'Telepon: +62 21 452 3273',
        'contact_prefix' => 'Silakan hubungi sekolah sebelum berkunjung. ',
        'contact_link' => 'Hubungi melalui WhatsApp',
        'contact_suffix' => '.',
    ],
    'mandarin_program' => [
        'title' => 'Program Mandarin Jangka Pendek',
        'summary' => $summary,
        'seo_title' => 'Program Mandarin Jangka Pendek',
        'body' => $standardPage,
    ],
];
