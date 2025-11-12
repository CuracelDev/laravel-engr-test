<?php

namespace Database\Factories;

use App\Models\Insurer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Claim Factory
 * 
 * Generates fake claim data for testing purposes.
 * Creates claims with random providers, specialties, priorities, and items.
 */
class ClaimFactory extends Factory
{
    public function definition(): array
    {
        return [
            'provider_name' => fake()->company(),
            'insurer_id' => Insurer::factory(),
            'encounter_date' => fake()->date(),
            'submission_date' => now()->toDateString(),
            'priority_level' => fake()->numberBetween(1, 5),
            'specialty' => fake()->randomElement(['cardiology', 'orthopedics', 'neurology', 'general']),
            'items' => [
                ['name' => 'Service 1', 'unit_price' => 100, 'quantity' => 1, 'subtotal' => 100]
            ],
            'total_amount' => 100,
            'batch_id' => null,
            'batch_date' => null,
            'processing_cost' => null,
            'processed' => false,
        ];
    }
}