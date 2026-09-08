# PRD - Redesign Website Jakarta Taipei School (JTS)

Tanggal analisis: 2026-09-06

## 1. Ringkasan

Website lama Jakarta Taipei School di [https://www.jtsid.org/](https://www.jtsid.org/) berisi konten profil sekolah, informasi kampus, halaman kepala sekolah, berita, tim/staf, kurikulum, penerimaan siswa, program bahasa Mandarin, pertukaran budaya, newsletter, prestasi, kalender, dan unduhan. Berdasarkan `web.pdf`, project yang diminta adalah memperbarui website lama agar tampil lebih layak sebagai website institusi/brand sekolah, mempertahankan konten lama yang masih relevan, serta menambahkan CMS/admin panel untuk mengelola konten per departemen dan beberapa jenis daftar file baru.

Dokumen ini mengasumsikan bahwa `web.pdf` adalah arahan requirement awal dari pihak sekolah/vendor sebelumnya kepada Aries. Karena PDF berbentuk scan sketsa, beberapa istilah masih perlu konfirmasi sebelum development final.

## 2. Tujuan Produk

- Membuat website sekolah yang modern, responsif, dan mudah dipakai oleh calon orang tua, siswa, staf sekolah, alumni, dan publik.
- Memigrasikan atau mempertahankan konten penting dari website lama.
- Menambahkan admin panel berbasis hak akses departemen.
- Mendukung upload file terstruktur untuk halaman akademik dan News & Media.
- Memperbaiki tampilan lama dengan CSS baru, layout brand website, dan encoding UTF-8.
- Menambahkan jalur kontak cepat: admission telephone, Instagram QR, WhatsApp QR, TikTok QR, dan footer contact.
- Mendukung tiga bahasa aktif: English, Mandarin, dan Indonesian, dengan English sebagai bahasa default.

## 3. Sumber Analisis

- Website lama: [Homepage JTS](https://www.jtsid.org/)
- Contoh halaman lama: [About](https://www.jtsid.org/about01.php), [Board of Directors](https://www.jtsid.org/about02.php), [Management](https://www.jtsid.org/about03.php), [Campus Headline](https://www.jtsid.org/headlines.php), [Staff](https://www.jtsid.org/team01.php), [Curriculum](https://www.jtsid.org/course01.php), [Admissions](https://www.jtsid.org/student.php), [Calendar/Downloads](https://www.jtsid.org/infomation06.php), [Links/Teaching Plan](https://www.jtsid.org/links.php)
- Requirement tambahan: `web.pdf` di workspace.

## 4. Target Pengguna

- Calon orang tua/siswa: mencari profil sekolah, admission, kurikulum, foto, berita, kontak, dan program Mandarin.
- Orang tua/siswa aktif: mencari kalender, classroom regulations, syllabus, textbook, lesson progress, weekly news, newsletter, file download.
- Admin sekolah/departemen: membuat dan memperbarui konten sesuai kewenangan.
- Kepala sekolah/manajemen: menampilkan profil institusi, pesan kepala sekolah, staf, dan prestasi.
- Publik/alumni/mitra: membaca berita, aktivitas, international exchange, dan university acceptance.

## 5. Scope MVP

### 5.1 Frontend Publik

| Modul | Kebutuhan | Fitur |
| --- | --- | --- |
| Header dan navigasi | Struktur menu baru dari `web.pdf` dan konten lama | Main menu, dropdown/submenu, mobile menu, language switch bila tetap dipakai |
| Utility contact | Di PDF tertulis Admission tel, IG QR, wha QR, TikTok QR | Nomor admission, tombol/QR Instagram, WhatsApp, TikTok |
| About | Memakai konten lama dengan CSS baru | About/Introduction, Board of Directors, school profile/management |
| University Admission | Memakai konten lama "University Acceptance" | Daftar acceptance per tahun, bisa dikelola admin |
| Academics | Di PDF diminta all in one page | Section Mandarin, Indonesian/Pancasila/Religions, English dalam satu halaman |
| Admissions | Di PDF berisi Academics old, Short-term Mandarin Program old, link photo | Informasi admission, program Mandarin short-term, galeri/link foto, dokumen pendukung |
| News & Media | Redesign baru | Daftar berita/media, filter departemen, filter subject, pencarian, sort by date upload, pagination |
| Highlight slideshow | Di PDF: pic small scale untuk Competition dan International exchange | Carousel kecil, 1 topik 1 foto, maksimal 10 slide, gambar kecil 1/3 layar |
| Subject resources | Di PDF: sort day TK, SD, SMP, SMA | Daftar file per jenjang/departemen untuk Calendar, Classroom Regulations, Syllabus, Textbook, Lesson Progress, Weekly News |
| Footer | Di PDF: copyright, QR, address, telephone | Footer informasi sekolah, address, tel, QR IG/WhatsApp, social links |
| Multi-language | Konfirmasi terbaru | English default, Mandarin dan Indonesian tersedia melalui language switch |

### 5.2 Admin Panel

| Modul Admin | Kebutuhan | Fitur |
| --- | --- | --- |
| Login | Admin sekolah dan departemen | Login, logout, session timeout |
| User dan role | PDF: admin panel department users right | Super admin, content admin, department admin, viewer |
| Department rights | Hak akses per departemen | Admin hanya bisa membuat/mengubah konten departemennya, super admin bisa semua |
| Menu/pages CMS | Old page renew CSS dan konten lama | Edit static pages, publish/draft, SEO metadata |
| News & Media CMS | Upload file 15 MB/item, sort by date upload | CRUD berita/media, upload doc/pdf/rar, filter, search, pagination |
| Subject resources CMS | Calendar, Classroom Regulations, Syllabus, Textbook, Lesson Progress, Weekly News | CRUD daftar file, jenjang TK/SD/SMP/SMA, topik, tanggal, attachment |
| Slideshow CMS | 1 topic 1 pic, max 1 MB, auto override, disable upload saat kuota penuh | Upload/crop image, sort order, enable/disable, limit 10 |
| Contacts & QR CMS | IG/WhatsApp/TikTok QR | Upload QR, link social, nomor admission, footer tel/address |
| Background/student work | PDF: page background can upload Student Work pic | Upload gambar background/featured student work, validasi ukuran |
| Audit log | Penting untuk sekolah | Catat create/update/delete, siapa dan kapan |

### 5.3 Multi-language Requirement

- Bahasa yang wajib tersedia: English (`en`), Mandarin Traditional Chinese (`zh-TW`), dan Indonesian (`id`).
- Bahasa default publik: English.
- URL default English disarankan tanpa prefix, contoh `/about`; bahasa lain memakai prefix, contoh `/zh/about` dan `/id/about`.
- Jika terjemahan Mandarin atau Indonesian belum tersedia, halaman boleh fallback ke English dengan indikator admin, tetapi publik sebaiknya tidak melihat teks campur acak.
- Semua menu, halaman statis, berita, resource, metadata SEO, tombol, label form, dan pesan validasi harus mendukung multi-bahasa.
- File upload bisa dipakai lintas bahasa bila dokumennya sama, atau dibuat per bahasa bila file berbeda.
- CMS harus menampilkan status kelengkapan terjemahan per konten: English, Mandarin, Indonesian.

## 6. Kebutuhan Per Menu dan Submenu

### 6.1 About

Konten lama yang relevan:
- About/Introduction dari [about01.php](https://www.jtsid.org/about01.php): sejarah berdiri, tujuan, sistem sekolah, guru, kurikulum umum.
- Board of Directors dari [about02.php](https://www.jtsid.org/about02.php): daftar ketua/dewan pengurus per periode.
- Management/profile dari [about03.php](https://www.jtsid.org/about03.php): kondisi sekolah, kepala sekolah, sumber guru, jumlah kelas/siswa.

Kebutuhan baru:
- Renew CSS untuk halaman lama.
- Layout brand website yang lebih bersih.
- Konten dapat diedit dari CMS.
- Semua teks disimpan UTF-8.

### 6.2 University Admission

Konten lama yang relevan:
- Halaman lama "University Acceptance" atau "High School Graduate Admission List", saat ini terhubung ke [prize01.php](https://www.jtsid.org/prize01.php).

Fitur:
- List penerimaan universitas per tahun akademik.
- Field minimal: tahun, jalur penerimaan, nama siswa, universitas, jurusan/program, negara, catatan.
- Search/filter tahun dan universitas.
- Admin dapat import/manual input.

### 6.3 Academics

Konten lama yang relevan:
- Taiwan/Mandarin curriculum dari [course01.php](https://www.jtsid.org/course01.php).
- Indonesian/Pancasila/Religion dari [course02.php](https://www.jtsid.org/course02.php).
- English curriculum dari [course03.php](https://www.jtsid.org/course03.php).

Kebutuhan baru dari PDF:
- Mandarin, Indonesian/Pancasila/Religions, dan English digabung dalam satu halaman "Academics".
- Tampilan berupa section/tab/accordion agar mudah dibaca.
- Konten lama dipindahkan dan dirapikan.

### 6.4 Admissions

Konten lama yang relevan:
- Admissions list dari [student.php](https://www.jtsid.org/student.php).
- Admissions Q&A dari file `download/qanda.pdf`.
- Short-term Mandarin/Chinese learning dari [spread.php](https://www.jtsid.org/spread.php).

Kebutuhan baru:
- Menampilkan admission telephone di header.
- Menyediakan link photo atau galeri pendukung.
- Informasi pendaftaran harus mudah dicari: syarat, jadwal, file, hasil penerimaan, kontak.

### 6.5 News & Media

Konten lama yang relevan:
- Campus headlines dari [headlines.php](https://www.jtsid.org/headlines.php).
- Campus activity dari [infomation04.php](https://www.jtsid.org/infomation04.php).
- Cultural exchange dari [communicate.php](https://www.jtsid.org/communicate.php).
- Newsletter dari [message.php](https://www.jtsid.org/message.php).

Kebutuhan baru dari PDF:
- New design style.
- List dengan kolom/filter: Department, Subjects, Topic, Date.
- Sort by date upload.
- Search box.
- First 10 items per page.
- Pagination sampai halaman terakhir.
- Upload 1 file per item dengan format doc, pdf, rar, maksimal 15 MB.

### 6.6 Subject Resources

Ini adalah modul baru yang di PDF ditulis sebagai "Subjects: sort day tk, sd, smp, sma". Interpretasi yang disarankan: konten/file akademik dikelompokkan berdasarkan jenjang TK, SD, SMP, SMA dan jenis subject/resource.

Jenis resource:
- Calendar
- Classroom Regulations
- Syllabus
- Textbook
- Lesson Progress
- Weekly News

Fitur:
- Tiap resource memiliki halaman list sendiri.
- Bisa filter jenjang: TK, SD, SMP, SMA.
- Bisa filter departemen bila diperlukan.
- Upload file maksimal 15 MB/file.
- List menampilkan topik, tanggal upload, jenjang, departemen, tombol download.

### 6.7 Campus/Information

Konten lama yang relevan:
- Location/traffic dari [infomation01.php](https://www.jtsid.org/infomation01.php).
- Extension numbers dari [infomation02.php](https://www.jtsid.org/infomation02.php).
- Campus photo guide dari [infomation03.php](https://www.jtsid.org/infomation03.php).
- Campus activity dari [infomation04.php](https://www.jtsid.org/infomation04.php).
- Campus rules dari [infomation05.php](https://www.jtsid.org/infomation05.php).
- Calendar download dari [infomation06.php](https://www.jtsid.org/infomation06.php).

Rekomendasi:
- Beberapa konten masuk ke About/Contact, News & Media, dan Subject Resources agar menu lebih ringkas.
- Campus rules lama bisa menjadi "Classroom Regulations" atau tetap "School Regulations" sesuai konfirmasi.

### 6.8 Staff

Konten lama:
- Secretary Office, Academic, Student Affairs, International Languages, Kindergarten, Chinese Learning Center, General Affairs, Accounting, Human Resource dari halaman [team01.php](https://www.jtsid.org/team01.php) sampai `team09.php`.

Fitur:
- Profil staf per departemen.
- Field: nama, jabatan, extension, job description, foto opsional, urutan tampil.
- Admin departemen dapat mengubah staf departemennya sendiri.

## 7. Non Functional Requirements

- Stack sesuai PDF: PHP + MySQL + Bootstrap + jQuery versi baru.
- Disarankan PHP 8.2+, MySQL 8/MariaDB 10.6+, Bootstrap 5.3, jQuery hanya bila masih diperlukan.
- Encoding seluruh halaman dan database: UTF-8/utf8mb4.
- Multi-language: English sebagai default locale; Mandarin dan Indonesian sebagai locale tambahan.
- Upload security: validasi MIME, ekstensi, ukuran, rename file, storage non-executable, scan/deny PHP upload.
- Responsif mobile/tablet/desktop.
- Admin panel wajib menggunakan CSRF protection, password hashing, role-based access control.
- SEO dasar: title, meta description, canonical URL, Open Graph image.
- Accessibility dasar: alt text gambar, contrast cukup, navigasi keyboard.
- Backup database dan file upload.

## 8. Acceptance Criteria

- Semua menu utama baru tampil di desktop dan mobile.
- Website tersedia dalam English, Mandarin, dan Indonesian; English menjadi default saat user membuka root URL.
- Konten lama penting dapat diakses atau dipetakan ke menu baru.
- Admin dapat login dan mengelola konten sesuai hak akses departemen.
- News & Media dapat membuat item baru dengan 1 attachment doc/pdf/rar maksimal 15 MB.
- Subject Resources memiliki 6 jenis halaman list dan filter jenjang TK/SD/SMP/SMA.
- Slideshow menampilkan maksimal 10 item, 1 topik 1 gambar, ukuran upload maksimal 1 MB.
- Search dan sort by upload date bekerja di News & Media dan resource list.
- Footer menampilkan copyright, address, telephone, QR Instagram, QR WhatsApp, dan link social.
- Halaman tidak lagi menampilkan komentar/kode internal PHP/HTML seperti yang muncul di beberapa halaman lama.

## 9. Asumsi dan Pertanyaan untuk Aries

- "sort day tk, sd, smp, sma" kemungkinan maksudnya sort/filter by grade level: TK, SD, SMP, SMA. Perlu konfirmasi.
- "1Topic 1pic SlideShow auto override 1MB disable upload" kemungkinan maksudnya setiap topik slideshow punya 1 gambar, upload maksimal 1 MB, dan upload dinonaktifkan jika sudah mencapai limit. Perlu konfirmasi apakah "auto override" berarti file lama otomatis diganti saat upload baru.
- "Admissions - Academics (eng old)" agak ambigu. Perlu diputuskan apakah halaman lama Academics English masuk ke Admissions atau tetap di Academics.
- "Link photo" di menu Admissions perlu dikonfirmasi: link ke galeri foto, album eksternal, atau upload foto admission.
- "javascript prompts Congratulations" perlu dikonfirmasi: pop-up ucapan selamat di halaman prestasi/pengumuman atau animasi/modal di homepage.

## 10. Strategi Multi-bahasa

- Gunakan English sebagai canonical/default content. Setiap konten baru wajib dibuat minimal dalam English sebelum bisa publish.
- Simpan field yang diterjemahkan di tabel translation terpisah, misalnya `page_translations`, `post_translations`, dan `subject_resource_translations`.
- Simpan field non-bahasa di tabel utama, misalnya slug canonical, status, tanggal publish, department, grade level, file, image, sort order.
- Menu juga perlu translation table agar label navigasi bisa berbeda per bahasa tanpa membuat struktur menu terpisah.
- Gunakan fallback berurutan: requested locale -> English. Untuk halaman publik, fallback sebaiknya tetap halus; untuk admin, tampilkan badge "Missing translation".
- Hindari membuat 3 record konten terpisah untuk 3 bahasa karena akan menyulitkan sorting, upload, permission, dan relasi antar konten.
- Untuk SEO, setiap halaman perlu `hreflang`: `en`, `zh-TW`, `id`, dan `x-default` ke English.
