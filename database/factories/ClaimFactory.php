<?php

namespace Database\Factories;

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
            'provider_name' => $this->faker->name,
            'insurer_id' => Insurer::factory(),
            'encounter_date' => $this->faker->date(),
            'submission_date' => now(),
            'priority' => $this->faker->numberBetween(1, 5),
            'specialty' => 'general',
            'amount' => $this->faker->randomFloat(2, 50, 1000),
            'status' => 'pending',
        ];
    }
}
