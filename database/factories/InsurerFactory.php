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
            'name' => $this->faker->company,
            'code' => $this->faker->unique()->bothify('??###'),
            'min_batch_size' => 10,
            'max_batch_size' => 100,
            'daily_processing_capacity' => 500,
            'date_preference' => 'encounter',
            'specialty_factors' => [],
            'email' => $this->faker->safeEmail,
        ];
    }
}
