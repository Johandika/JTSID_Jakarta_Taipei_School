<?php

namespace Database\Factories;

use App\Models\Director;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Director>
 */
class DirectorFactory extends Factory
{
    protected $model = Director::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => [
                'en' => $name,
                'id' => $name,
                'zh-TW' => $name,
            ],
            'role' => [
                'en' => fake()->jobTitle(),
                'id' => fake()->jobTitle(),
                'zh-TW' => fake()->jobTitle(),
            ],
            'photo' => 'images/directors/director-1.jpg',
            'education' => [
                'en' => [fake()->sentence(4), fake()->sentence(4)],
                'id' => [fake()->sentence(4), fake()->sentence(4)],
                'zh-TW' => [fake()->sentence(4), fake()->sentence(4)],
            ],
            'bio' => [
                'en' => fake()->paragraph(),
                'id' => fake()->paragraph(),
                'zh-TW' => fake()->paragraph(),
            ],
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
