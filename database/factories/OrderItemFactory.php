<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item' => $this->faker->word,
            'unit_price' => $this->faker->randomFloat(2, 10, 500),
            'qty' => $this->faker->numberBetween(1, 5),
            'subtotal' => function (array $attributes) {
                return $attributes['unit_price'] * $attributes['qty'];
            },
        ];
    }
}
