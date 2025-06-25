<?php

namespace Database\Factories;

use App\Models\User;
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
            'provider_id' => User::factory(),
            'encounter_date' => now()->subDays(rand(1, 30)),
            'specialty' => fake()->randomElement(config('constants.specialists')),
            'priority_level' => rand(1, 5),
            'total_amount' => rand(1000, 10000),
            'processed_at' => null,
            'submission_date' => now(),
        ];
    }
}
