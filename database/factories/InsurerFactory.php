<?php

namespace Database\Factories;

use App\Enums\DatePreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class InsurerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'code' => fake()->unique()->regexify('INS-[A-Z]'),
            'email' => fake()->unique()->companyEmail(),
            'min_batch_size' => fake()->numberBetween(5, 10),
            'max_batch_size' => fake()->numberBetween(40, 100),
            'daily_capacity_limit' => fake()->numberBetween(500, 2000),
            'date_preference' => fake()->randomElement([
                DatePreference::ENCOUNTER,
                DatePreference::SUBMISSION,
            ]),
            'base_processing_cost' => fake()->randomFloat(2, 2500, 4000),
            'time_of_month_multiplier_start' => fake()->randomFloat(2, 0.15, 0.25),
            'time_of_month_multiplier_end' => fake()->randomFloat(2, 0.45, 0.55),
            'priority_cost_multipliers' => [
                1 => fake()->randomFloat(2, 0.6, 0.8),
                2 => fake()->randomFloat(2, 0.8, 1.0),
                3 => fake()->randomFloat(2, 1.0, 1.2),
                4 => fake()->randomFloat(2, 1.2, 1.5),
                5 => fake()->randomFloat(2, 1.5, 2.0),
            ],
            'specialty_cost_multipliers' => [
                'cardiology' => fake()->randomFloat(2, 1.2, 1.5),
                'orthopedics' => fake()->randomFloat(2, 1.1, 1.3),
            ],
            'claim_value_multipliers' => [
                [
                    'min' => 0,
                    'max' => 1000,
                    'multiplier' => fake()->randomFloat(2, 0.8, 0.9),
                ],
                [
                    'min' => 1000,
                    'max' => 5000,
                    'multiplier' => fake()->randomFloat(2, 1.0, 1.1),
                ],
                [
                    'min' => 5000,
                    'max' => null,
                    'multiplier' => fake()->randomFloat(2, 1.5, 1.8),
                ],
            ],
        ];
    }

    public function withDatePreference(DatePreference $preference): static
    {
        return $this->state([
            'date_preference' => $preference,
        ]);
    }
}
