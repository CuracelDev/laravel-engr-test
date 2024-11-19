<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,  // Random name for the item
            'qty' => $this->faker->numberBetween(1, 10),  // Random quantity
            'unit_price' => $this->faker->randomFloat(2, 5, 1000),  // Random unit price
        ];
    }
}
