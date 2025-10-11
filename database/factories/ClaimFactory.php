<?php

namespace Database\Factories;

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
            'insurer_id' => 1, 
            'provider_name' => 'Test Provider',
            'encounter_date' => $this->faker->date(),
            'submission_date' => $this->faker->date(),
            'specialty' => 'Test Specialty',
            'priority_level' => 3,
            'total_amount' => 100,
        ];
    }
}
