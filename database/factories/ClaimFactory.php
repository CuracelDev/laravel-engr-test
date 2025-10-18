<?php

namespace Database\Factories;

use App\Enums\ClaimStatus;
use App\Enums\MedicalSpecialty;
use App\Models\Insurer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Claim>
 */
class ClaimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'insurer_id' => Insurer::factory(),
            'provider_name' => 'Dr. ' . fake()->name(),
            'encounter_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'submission_date' => now(),
            'priority_level' => fake()->numberBetween(1, 5),
            'specialty' => fake()->randomElement(MedicalSpecialty::all()),
            'total_amount' => fake()->randomFloat(2, 100, 10000),
            'status' => ClaimStatus::PENDING,
        ];
    }
}

