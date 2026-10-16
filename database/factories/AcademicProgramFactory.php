<?php

namespace Database\Factories;

use App\Models\AcademicProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicProgram>
 */
class AcademicProgramFactory extends Factory
{
    protected $model = AcademicProgram::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(2, true);

        return [
            'image' => null,
            'label' => [
                'en' => strtoupper(fake()->words(2, true)),
                'id' => strtoupper(fake()->words(2, true)),
                'zh-TW' => '教育階段',
            ],
            'title' => [
                'en' => ucfirst($title),
                'id' => ucfirst($title),
                'zh-TW' => ucfirst($title).'學程',
            ],
            'description' => [
                'en' => fake()->paragraph(),
                'id' => fake()->paragraph(),
                'zh-TW' => fake()->paragraph(),
            ],
            'url' => '/academics',
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
