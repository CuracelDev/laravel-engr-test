<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Insurer>
 */
class InsurerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
          return [
            'name'                     => $this->faker->company . ' Insurance',
            'code'                     => strtoupper($this->faker->bothify('INS-###')),
            'email'                    => $this->faker->unique()->companyEmail(),
            'batch_date_pref'          => $this->faker->randomElement(['encounter', 'submission']),
            'max_batch_size'           => $this->faker->numberBetween(5, 20),
            'min_batch_size'           => $this->faker->numberBetween(2, 5),
            'specialty_efficiency'     => json_encode(['Cardiology' => 0.9, 'Dermatology' => 1.1]),
            'priority_cost_multiplier' => json_encode(['1.0' => 1.0, '2.0' => 1.2]),
        ];
    }
}
