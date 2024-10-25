<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hmo>
 */
class HmoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company, 
            'email' => $this->faker->unique()->safeEmail,
            'code' => $this->faker->unique()->word,
            'batches_type' => $this->faker->randomElement(['encounter', 'actual']), 
        ];
    }
}
