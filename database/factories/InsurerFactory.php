<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Insurer>
 */
class InsurerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'code' => fake()->unique()->bothify('INS-#########'),
            'email' => fake()->unique()->safeEmail(),
            'specialty_efficiencies' => [],
            'daily_capacity' => rand(1000, 10000),
            'min_batch_size' => rand(1, 10),
            'max_batch_size' => rand(20, 1000),
            'batch_date_preference' => fake()->randomElement(['submission', 'encounter']),
        ];
    }
}
