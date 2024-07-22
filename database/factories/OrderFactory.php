<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hmo_code' => $this->faker->unique()->word,
            'provider_name' => $this->faker->name,
            'encounter_date' => $this->faker->dateTimeThisYear,
            'total_price' => $this->faker->randomFloat(2, 100, 10000),
        ];
    }
}
