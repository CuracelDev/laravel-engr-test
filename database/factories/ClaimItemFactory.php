<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClaimItem>
 */
class ClaimItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'unit_price' => fake()->randomFloat(2, 1000, 1000000),
            'quantity' => rand(1, 10),
            'sub_total' => function ($attributes) {
                return $attributes['unit_price'] * $attributes['quantity'];
            },
        ];
    }
}
