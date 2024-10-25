<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hmos = [
            ['name' => 'HMO A', 'code' => 'HMO-A'],
            ['name' => 'HMO B', 'code' => 'HMO-B'],
            ['name' => 'HMO C', 'code' => 'HMO-C'],
            ['name' => 'HMO D', 'code' => 'HMO-D'],
        ];

        $providers = [
            ['name' => 'Provider A', 'code' => 'Provider-A'],
            ['name' => 'Provider B', 'code' => 'Provider-B'],
            ['name' => 'Provider C', 'code' => 'Provider-C'],
            ['name' => 'Provider D', 'code' => 'Provider-D'],
        ];

       
        $hmo = $this->faker->randomElement($hmos);
        $provider = $this->faker->randomElement($providers);

       
        return [
            'order_id' => $this->faker->uuid, 
            'encounter_date' => $this->faker->dateTimeBetween('2024-09-01', '2024-10-31'),
            'provider_code' => $provider['code'],
            'hmo_code' => $hmo['code'],
            'total' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (\App\Models\Order $order) {
            \App\Models\OrderItem::factory()->count(2)->create([
                'order_id' => $order->order_id,
            ]);
        });
    }
}
