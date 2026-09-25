<?php

namespace Database\Factories;

use App\Models\AdmissionFee;
use App\Models\GradeLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdmissionFee>
 */
class AdmissionFeeFactory extends Factory
{
    protected $model = AdmissionFee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grade_level_id' => GradeLevel::first()?->id ?? GradeLevel::factory(),
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
            'is_active' => true,
        ];
    }
}
