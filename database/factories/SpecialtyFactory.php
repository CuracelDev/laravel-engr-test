<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class SpecialtyFactory extends Factory
{
    protected $model = Specialty::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'speciality' => $this->faker->name,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
