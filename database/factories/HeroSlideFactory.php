<?php

namespace Database\Factories;

use App\Models\HeroSlide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HeroSlide>
 */
class HeroSlideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => 'images/seed-campus.jpg',
            'title' => [
                'en' => fake()->sentence(4),
                'id' => fake()->sentence(4),
                'zh-TW' => '卓越教育示範',
            ],
            'description' => [
                'en' => fake()->paragraph(),
                'id' => fake()->paragraph(),
                'zh-TW' => '致力於全人教育與全球競爭力。',
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
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
