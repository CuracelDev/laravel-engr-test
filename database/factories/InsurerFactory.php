<?php

namespace Database\Factories;

use App\Models\Insurer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Insurer>
 */
class InsurerFactory extends Factory
{
    protected $model = Insurer::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Str::uuid(),
            'name' => fake()->name(),
            'maximum_num_of_batch' => rand(5,10),
            'minimum_num_of_batch' => rand(2,4),
            'daily_limits' => 17,
            'total_processing_cost' =>'102233', 
            'date_preference' => 'encounter',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
