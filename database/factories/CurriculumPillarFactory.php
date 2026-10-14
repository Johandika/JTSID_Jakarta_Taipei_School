<?php

namespace Database\Factories;

use App\Models\CurriculumPillar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CurriculumPillar>
 */
class CurriculumPillarFactory extends Factory
{
    protected $model = CurriculumPillar::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(2, true);

        return [
            'icon' => null,
            'title' => [
                'en' => ucfirst($title),
                'id' => 'Kurikulum '.ucfirst($title),
                'zh-TW' => ucfirst($title).'課程',
            ],
            'description' => [
                'en' => fake()->paragraph(),
                'id' => fake()->paragraph(),
                'zh-TW' => fake()->paragraph(),
            ],
            'tags' => [
                'en' => [fake()->word(), fake()->word()],
                'id' => [fake()->word(), fake()->word()],
                'zh-TW' => [fake()->word(), fake()->word()],
            ],
            'color_theme' => fake()->randomElement(['red', 'green', 'blue', 'emerald', 'amber', 'purple']),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
