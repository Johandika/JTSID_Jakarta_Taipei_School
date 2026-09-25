<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'subject' => fake()->randomElement([
                ContactMessage::SUBJECT_ADMISSIONS,
                ContactMessage::SUBJECT_MANDARIN_PROGRAM,
                ContactMessage::SUBJECT_GENERAL,
                ContactMessage::SUBJECT_ACADEMICS,
                ContactMessage::SUBJECT_OTHER,
            ]),
            'message' => fake()->paragraph(),
            'status' => ContactMessage::STATUS_UNREAD,
            'admin_notes' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
