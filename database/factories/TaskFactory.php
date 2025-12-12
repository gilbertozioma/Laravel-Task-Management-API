<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'in-progress', 'completed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'due_date' => function () {
                $dt = fake()->optional()->dateTimeBetween('+1 days', '+1 month');
                return $dt ? $dt->format('Y-m-d H:i:s') : null;
            },
            'user_id' => User::factory(),
        ];
    }
}
