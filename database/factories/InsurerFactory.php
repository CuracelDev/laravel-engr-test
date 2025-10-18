<?php

namespace Database\Factories;

use App\Enums\DatePreference;
use App\Enums\MedicalSpecialty;
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
        $specialties = MedicalSpecialty::all();
        $multipliers = [];
        
        foreach ($specialties as $specialty) {
            $multipliers[$specialty] = fake()->randomFloat(2, 0.7, 1.5);
        }

        return [
            'name' => fake()->company() . ' Insurance',
            'code' => 'INS-' . strtoupper(fake()->unique()->lexify('???')),
            'base_processing_cost' => fake()->randomFloat(2, 10, 30),
            'daily_capacity' => fake()->numberBetween(50, 150),
            'min_batch_size' => fake()->numberBetween(1, 5),
            'max_batch_size' => fake()->numberBetween(20, 60),
            'date_preference' => fake()->randomElement([DatePreference::ENCOUNTER, DatePreference::SUBMISSION]),
            'specialty_efficiency_multipliers' => $multipliers,
        ];
    }
}

