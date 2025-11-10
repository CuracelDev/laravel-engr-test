<?php

namespace Database\Factories;

use App\Enums\ClaimStatus;
use App\Enums\PriorityLevel;
use App\Enums\Specialty;
use App\Models\Insurer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ClaimFactory extends Factory
{
    public function definition(): array
    {
        $encounterDate = fake()->dateTimeBetween('-60 days', '-1 day');
        $submissionDate = Carbon::parse($encounterDate)->addDays(fake()->numberBetween(1, 14));

        return [
            'insurer_id' => Insurer::factory(),
            'provider_name' => fake()->company(),
            'encounter_date' => $encounterDate,
            'submission_date' => $submissionDate,
            'priority_level' => fake()->randomElement(PriorityLevel::cases()),
            'specialty' => fake()->randomElement(Specialty::cases()),
            'status' => ClaimStatus::PENDING,
            'total_amount' => fake()->randomFloat(2, 100, 20000),
            'processing_cost' => fake()->randomFloat(2, 10, 500),
            'batch_id' => null,
        ];
    }
}
