<?php

namespace Database\Factories;

use App\Models\MandarinProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MandarinProgram>
 */
class MandarinProgramFactory extends Factory
{
    protected $model = MandarinProgram::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement([
                MandarinProgram::CATEGORY_MANDARIN_CLASS,
                MandarinProgram::CATEGORY_TOCFL,
                MandarinProgram::CATEGORY_WORKSHOP_EVENT,
            ]),
            'title' => [
                'en' => fake()->sentence(3),
                'id' => fake()->sentence(3),
                'zh-TW' => '華語學習課程',
            ],
            'short_description' => [
                'en' => fake()->sentence(8),
                'id' => fake()->sentence(8),
                'zh-TW' => '短期華語文相關培訓與認證活動。',
            ],
            'description' => [
                'en' => fake()->paragraph(),
                'id' => fake()->paragraph(),
                'zh-TW' => '提供全方位的專業華語師資與豐富的文化體驗活動。',
            ],
            'cover_image' => null,
            'gallery_images' => [],
            'program_date_label' => ['en' => 'Program Period'],
            'program_start_date' => now()->addMonth(),
            'program_end_date' => now()->addMonths(2),
            'registration_start_date' => now()->subDays(5),
            'registration_end_date' => now()->addDays(20),
            'location' => [
                'en' => 'Jakarta Taipei School',
                'id' => 'Jakarta Taipei School',
                'zh-TW' => '印尼雅加達臺灣學校',
            ],
            'registration_url' => 'https://example.com/register',
            'contact_info' => [
                'en' => 'huayu@jts.sch.id',
                'id' => 'huayu@jts.sch.id',
            ],
            'extra_info_title' => [
                'en' => 'Available Levels',
                'id' => 'Tingkat Kelas',
            ],
            'extra_info_items' => [
                ['label' => 'Level 1', 'value' => 'Novice Low', 'color' => 'rose'],
                ['label' => 'Level 2', 'value' => 'Novice High', 'color' => 'amber'],
                ['label' => 'Level 3', 'value' => 'Intermediate Low', 'color' => 'blue'],
                ['label' => 'Level 4', 'value' => 'Intermediate High', 'color' => 'emerald'],
            ],
            'status_override' => MandarinProgram::STATUS_AUTO,
            'show_details' => true,
            'is_active' => true,
        ];
    }
}
