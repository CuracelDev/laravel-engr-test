<?php

namespace Database\Factories;

use App\Enums\BatchStatus;
use App\Models\Batch;
use App\Models\Insurer;
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
        $providerName = 'Dr. ' . fake()->name();
        $batchDate = fake()->dateTimeBetween('now', '+7 days');

        return [
            'identifier' => Batch::generateIdentifier($providerName, $batchDate->format('Y-m-d')),
            'insurer_id' => Insurer::factory(),
            'batch_date' => $batchDate,
            'total_claims' => 0,
            'total_amount' => 0,
            'status' => BatchStatus::PENDING,
        ];
    }
}

