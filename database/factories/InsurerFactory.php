<?php

namespace Database\Factories;

use App\Models\Insurer;
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
    protected $model = Insurer::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'code' => $this->faker->unique()->lexify('INS???'),
            'min_batch_size' => $this->faker->numberBetween(1, 5),
            'max_batch_size' => $this->faker->numberBetween(10, 50),
            'processing_costs' => json_encode([
                'day_1_to_10' => 0.2,
                'day_11_to_20' => 0.3,
                'day_21_to_30' => 0.4,
            ]),
        ];
    }
}
