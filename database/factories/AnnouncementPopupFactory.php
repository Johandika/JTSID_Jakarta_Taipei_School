<?php

namespace Database\Factories;

use App\Models\AnnouncementPopup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnnouncementPopup>
 */
class AnnouncementPopupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'body' => 'Congratulations to our school community.',
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
