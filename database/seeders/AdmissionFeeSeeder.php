<?php

namespace Database\Seeders;

use App\Models\AdmissionFee;
use App\Models\GradeLevel;
use Illuminate\Database\Seeder;

class AdmissionFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = GradeLevel::all()->keyBy('code');

        if ($levels->isEmpty()) {
            return;
        }

        $feesByLevel = [
            'tk' => [
                [
                    'name' => [
                        'id' => 'Uang Pendaftaran',
                        'en' => 'Registration Fee',
                        'zh-TW' => '報名費',
                    ],
                    'amount' => 'Rp. 1.000.000,-',
                    'payment_period' => [
                        'id' => 'satu kali pembayaran',
                        'en' => 'one-time payment',
                        'zh-TW' => '一次性繳納',
                    ],
                    'note' => null,
                    'icon' => 'file-text',
                    'icon_color' => 'green',
                    'sort_order' => 1,
                ],
                [
                    'name' => [
                        'id' => 'Uang Pembangunan',
                        'en' => 'Development Fee',
                        'zh-TW' => '建設發展費',
                    ],
                    'amount' => 'Rp. 5.000.000,-',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => [
                        'id' => "Untuk anak kedua Rp. 3.500.000,-\nAnak ketiga gratis",
                        'en' => "For second child Rp. 3,500,000,-\nThird child free",
                        'zh-TW' => "第二位子女 Rp. 3,500,000,-\n第三位子女免費",
                    ],
                    'icon' => 'building',
                    'icon_color' => 'blue',
                    'sort_order' => 2,
                ],
                [
                    'name' => [
                        'id' => 'Uang Sekolah',
                        'en' => 'Tuition Fee',
                        'zh-TW' => '學雜費',
                    ],
                    'amount' => 'Rp. 27.830.000,-',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => null,
                    'icon' => 'backpack',
                    'icon_color' => 'pink',
                    'sort_order' => 3,
                ],
                [
                    'name' => [
                        'id' => 'Uang Buku dan Kegiatan',
                        'en' => 'Books & Activity Fee',
                        'zh-TW' => '書籍與活動費',
                    ],
                    'amount' => '± Rp. 5–6 juta',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => null,
                    'icon' => 'book-open',
                    'icon_color' => 'amber',
                    'sort_order' => 4,
                ],
            ],
            'sd' => [
                [
                    'name' => [
                        'id' => 'Uang Pendaftaran',
                        'en' => 'Registration Fee',
                        'zh-TW' => '報名費',
                    ],
                    'amount' => 'Rp. 1.000.000,-',
                    'payment_period' => [
                        'id' => 'satu kali pembayaran',
                        'en' => 'one-time payment',
                        'zh-TW' => '一次性繳納',
                    ],
                    'note' => null,
                    'icon' => 'file-text',
                    'icon_color' => 'green',
                    'sort_order' => 1,
                ],
                [
                    'name' => [
                        'id' => 'Uang Pembangunan',
                        'en' => 'Development Fee',
                        'zh-TW' => '建設發展費',
                    ],
                    'amount' => 'Rp. 6.000.000,-',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => [
                        'id' => "Untuk anak kedua potongan 20%\nAnak ketiga potongan 50%",
                        'en' => "20% discount for second child\n50% discount for third child",
                        'zh-TW' => "第二位子女享八折優惠\n第三位子女享五折優惠",
                    ],
                    'icon' => 'building',
                    'icon_color' => 'blue',
                    'sort_order' => 2,
                ],
                [
                    'name' => [
                        'id' => 'Uang Sekolah',
                        'en' => 'Tuition Fee',
                        'zh-TW' => '學雜費',
                    ],
                    'amount' => 'Rp. 31.500.000,-',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => null,
                    'icon' => 'backpack',
                    'icon_color' => 'pink',
                    'sort_order' => 3,
                ],
                [
                    'name' => [
                        'id' => 'Uang Buku dan Kegiatan',
                        'en' => 'Books & Activity Fee',
                        'zh-TW' => '書籍與活動費',
                    ],
                    'amount' => '± Rp. 6–7 juta',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => null,
                    'icon' => 'book-open',
                    'icon_color' => 'amber',
                    'sort_order' => 4,
                ],
            ],
            'smp' => [
                [
                    'name' => [
                        'id' => 'Uang Pendaftaran',
                        'en' => 'Registration Fee',
                        'zh-TW' => '報名費',
                    ],
                    'amount' => 'Rp. 1.000.000,-',
                    'payment_period' => [
                        'id' => 'satu kali pembayaran',
                        'en' => 'one-time payment',
                        'zh-TW' => '一次性繳納',
                    ],
                    'note' => null,
                    'icon' => 'file-text',
                    'icon_color' => 'green',
                    'sort_order' => 1,
                ],
                [
                    'name' => [
                        'id' => 'Uang Pembangunan',
                        'en' => 'Development Fee',
                        'zh-TW' => '建設發展費',
                    ],
                    'amount' => 'Rp. 7.500.000,-',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => [
                        'id' => 'Tersedia beasiswa prestasi akademik bagi siswa berprestasi',
                        'en' => 'Academic achievement scholarships available for outstanding students',
                        'zh-TW' => '提供成績優異學生學術獎學金',
                    ],
                    'icon' => 'building',
                    'icon_color' => 'blue',
                    'sort_order' => 2,
                ],
                [
                    'name' => [
                        'id' => 'Uang Sekolah',
                        'en' => 'Tuition Fee',
                        'zh-TW' => '學雜費',
                    ],
                    'amount' => 'Rp. 35.200.000,-',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => null,
                    'icon' => 'backpack',
                    'icon_color' => 'pink',
                    'sort_order' => 3,
                ],
                [
                    'name' => [
                        'id' => 'Uang Buku dan Kegiatan',
                        'en' => 'Books & Activity Fee',
                        'zh-TW' => '書籍與活動費',
                    ],
                    'amount' => '± Rp. 7–8 juta',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => null,
                    'icon' => 'book-open',
                    'icon_color' => 'amber',
                    'sort_order' => 4,
                ],
            ],
            'sma' => [
                [
                    'name' => [
                        'id' => 'Uang Pendaftaran',
                        'en' => 'Registration Fee',
                        'zh-TW' => '報名費',
                    ],
                    'amount' => 'Rp. 1.000.000,-',
                    'payment_period' => [
                        'id' => 'satu kali pembayaran',
                        'en' => 'one-time payment',
                        'zh-TW' => '一次性繳納',
                    ],
                    'note' => null,
                    'icon' => 'file-text',
                    'icon_color' => 'green',
                    'sort_order' => 1,
                ],
                [
                    'name' => [
                        'id' => 'Uang Pembangunan',
                        'en' => 'Development Fee',
                        'zh-TW' => '建設發展費',
                    ],
                    'amount' => 'Rp. 8.500.000,-',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => [
                        'id' => 'Tersedia program beasiswa jalur prestasi akademik dan minat bakat',
                        'en' => 'Scholarships available for academic and special talent tracks',
                        'zh-TW' => '提供學術與多元專長卓越獎學金方案',
                    ],
                    'icon' => 'building',
                    'icon_color' => 'blue',
                    'sort_order' => 2,
                ],
                [
                    'name' => [
                        'id' => 'Uang Sekolah',
                        'en' => 'Tuition Fee',
                        'zh-TW' => '學雜費',
                    ],
                    'amount' => 'Rp. 38.900.000,-',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => null,
                    'icon' => 'backpack',
                    'icon_color' => 'pink',
                    'sort_order' => 3,
                ],
                [
                    'name' => [
                        'id' => 'Uang Buku dan Kegiatan',
                        'en' => 'Books & Activity Fee',
                        'zh-TW' => '書籍與活動費',
                    ],
                    'amount' => '± Rp. 8–9 juta',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => null,
                    'icon' => 'book-open',
                    'icon_color' => 'amber',
                    'sort_order' => 4,
                ],
            ],
            'international' => [
                [
                    'name' => [
                        'id' => 'Uang Pendaftaran',
                        'en' => 'Registration Fee',
                        'zh-TW' => '報名費',
                    ],
                    'amount' => 'Rp. 1.000.000,-',
                    'payment_period' => [
                        'id' => 'satu kali pembayaran',
                        'en' => 'one-time payment',
                        'zh-TW' => '一次性繳納',
                    ],
                    'note' => null,
                    'icon' => 'file-text',
                    'icon_color' => 'green',
                    'sort_order' => 1,
                ],
                [
                    'name' => [
                        'id' => 'Uang Pembangunan',
                        'en' => 'Development Fee',
                        'zh-TW' => '建設發展費',
                    ],
                    'amount' => 'Rp. 8.500.000,-',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => [
                        'id' => 'Tersedia program beasiswa jalur prestasi akademik dan minat bakat',
                        'en' => 'Scholarships available for academic and special talent tracks',
                        'zh-TW' => '提供學術與多元專長卓越獎學金方案',
                    ],
                    'icon' => 'building',
                    'icon_color' => 'blue',
                    'sort_order' => 2,
                ],
                [
                    'name' => [
                        'id' => 'Uang Sekolah',
                        'en' => 'Tuition Fee',
                        'zh-TW' => '學雜費',
                    ],
                    'amount' => 'Rp. 38.900.000,-',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => null,
                    'icon' => 'backpack',
                    'icon_color' => 'pink',
                    'sort_order' => 3,
                ],
                [
                    'name' => [
                        'id' => 'Uang Buku dan Kegiatan',
                        'en' => 'Books & Activity Fee',
                        'zh-TW' => '書籍與活動費',
                    ],
                    'amount' => '± Rp. 8–9 juta',
                    'payment_period' => [
                        'id' => 'pembayaran per semester (6 bulan)',
                        'en' => 'payment per semester (6 months)',
                        'zh-TW' => '每學期繳納（6個月）',
                    ],
                    'note' => null,
                    'icon' => 'book-open',
                    'icon_color' => 'amber',
                    'sort_order' => 4,
                ],
            ],
        ];

        foreach ($feesByLevel as $code => $fees) {
            $gradeLevel = $levels->get($code);
            if (! $gradeLevel) {
                continue;
            }

            foreach ($fees as $feeData) {
                AdmissionFee::updateOrCreate(
                    [
                        'grade_level_id' => $gradeLevel->id,
                        'name->id' => $feeData['name']['id'],
                    ],
                    [
                        'name' => $feeData['name'],
                        'amount' => $feeData['amount'],
                        'payment_period' => $feeData['payment_period'],
                        'note' => $feeData['note'],
                        'icon' => $feeData['icon'],
                        'icon_color' => $feeData['icon_color'],
                        'sort_order' => $feeData['sort_order'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
