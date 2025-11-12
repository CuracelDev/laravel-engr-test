<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Insurer Factory
 * 
 * Generates fake insurer data for testing purposes.
 * Creates insurers with random cost multipliers and constraints.
 */
class InsurerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'code' => fake()->unique()->regexify('[A-Z]{3}-[A-Z]'),
            'email' => fake()->companyEmail(),
            'specialty_costs' => [
                'cardiology' => 1.2,
                'orthopedics' => 1.0,
                'neurology' => 1.3,
                'general' => 0.9
            ],
            'priority_costs' => [1 => 1.0, 2 => 1.1, 3 => 1.3, 4 => 1.5, 5 => 2.0],
            'value_cost_multiplier' => 0.0001,
            'daily_capacity' => 100,
            'min_batch_size' => 2,
            'max_batch_size' => 10,
            'date_preference' => fake()->randomElement(['encounter', 'submission']),
        ];
    }
}