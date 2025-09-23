<?php

namespace Database\Factories;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimFactory extends Factory
{
    protected $model = Claim::class;

    public function definition(): array
    {
        return [
            'provider_name' => $this->faker->company(),
            'insurer_code' => $this->faker->randomElement(['INS-A', 'INS-B', 'INS-C', 'INS-D']),
            'encounter_date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'submission_date' => $this->faker->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
            'priority_level' => $this->faker->numberBetween(1, 5),
            'specialty' => $this->faker->randomElement(['cardiology', 'orthopedics', 'neurology', 'general', 'pediatrics']),
            'total_amount' => $this->faker->randomFloat(2, 50, 5000),
            'status' => $this->faker->randomElement(['pending', 'processing', 'processed', 'rejected']),
            'batch_id' => null,
        ];
    }

    /**
     * Create a pending claim
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'batch_id' => null,
        ]);
    }

    /**
     * Create a high priority claim
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_level' => 5,
        ]);
    }

    /**
     * Create a cardiology claim
     */
    public function cardiology(): static
    {
        return $this->state(fn (array $attributes) => [
            'specialty' => 'cardiology',
        ]);
    }

    /**
     * Create a high value claim
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_amount' => $this->faker->randomFloat(2, 5000, 20000),
        ]);
    }
}
