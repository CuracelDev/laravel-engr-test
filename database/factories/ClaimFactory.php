<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Claim>
 */
class ClaimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Claim::class;

    public function definition()
    {
        return [
            'insurer_id' => Insurer::factory(),
            'user_id' => User::factory(),
            'provider_name' => $this->faker->company,
            'encounter_date' => $this->faker->date(),
            'specialty' => $this->faker->word,
            'priority_level' => $this->faker->numberBetween(1, 5),
            'total_amount' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
