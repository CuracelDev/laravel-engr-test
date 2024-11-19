<?php

namespace Database\Factories;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Claim>
 */
class ClaimFactory extends Factory
{
    protected $model = Claim::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_name' => fake()->name(),
            'encounter_date' =>  fake()->dateTimeBetween('-1 year', 'now'),
            'priority' =>  fake()->randomElement(['1', '2', '3','4','5']),
            'total_value' =>  fake()->randomFloat(2, 100, 5000),
        ];
    }
}
