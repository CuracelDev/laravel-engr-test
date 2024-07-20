<?php

namespace Database\Factories;

use App\Enums\BatchStatusEnum;
use App\Models\Hmo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Batch>
 */
class BatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hmo_id' => Hmo::factory()->create()->id,
            'name' => $this->faker->name,
            'month' => $this->faker->monthName,
            'year' => $this->faker->year,
            'status' => BatchStatusEnum::PENDING->value,
            'processed_at' => null,
        ];
    }
}
