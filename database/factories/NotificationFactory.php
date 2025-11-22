<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'message' => $this->faker->sentence(12),
            'type' => $this->faker->randomElement(['general', 'breaking', 'reminder']),
            'data' => null,
            'read_at' => null,
        ];
    }
}
