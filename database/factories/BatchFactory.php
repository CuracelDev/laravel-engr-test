<?php

namespace Database\Factories;

use App\Models\Batch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        $provider = $this->faker->company();
        $date = $this->faker->dateTimeBetween('-30 days', 'now');

        return [
            'provider_name' => $provider,
            'insurer_code' => $this->faker->randomElement(['INS-A', 'INS-B', 'INS-C', 'INS-D']),
            'batch_date' => $date->format('Y-m-d'),
            'batch_identifier' => Batch::generateIdentifier($provider, $date->format('Y-m-d')),
            'claims_count' => $this->faker->numberBetween(1, 25),
            'total_amount' => $this->faker->randomFloat(2, 100, 50000),
            'processing_cost' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement(['pending', 'processing', 'processed']),
            'processed_at' => null,
        ];
    }

    /**
     * Create a pending batch
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'processed_at' => null,
        ]);
    }

    /**
     * Create a processing batch
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
            'processed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Create a processed batch
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processed',
            'processed_at' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
