<?php

namespace Database\Factories;

use App\Enums\BatchStatus;
use App\Models\Insurer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BatchFactory extends Factory
{
    public function definition(): array
    {
        $batchDate = $this->faker->dateTimeBetween('-30 days', '+30 days');

        return [
            'batch_identifier' => $this->faker->unique()->regexify('[A-Z]{4}\d{6}'),
            'insurer_id' => Insurer::factory(),
            'provider_name' => $this->faker->company(),
            'batch_date' => $batchDate,
            'status' => $this->faker->randomElement(BatchStatus::cases()),
            'total_claims' => $this->faker->numberBetween(0, 50),
            'total_amount' => $this->faker->randomFloat(2, 1000, 50000),
            'total_processing_cost' => $this->faker->randomFloat(2, 100, 5000),
            'processing_date' => Carbon::parse($batchDate)->addDay(),
            'completed_at' => $this->faker->optional(0.3)->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
