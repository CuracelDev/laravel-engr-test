<?php

namespace Database\Factories;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimItemFactory extends Factory
{
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 10, 1000);
        $quantity = fake()->numberBetween(1, 10);
        $subtotal = $unitPrice * $quantity;

        return [
            'name' => fake()->randomElement([
                'X-Ray',
                'Blood Test',
                'MRI Scan',
                'Consultation Fee',
                'Surgery Charge',
                'Medication',
                'Therapy Session',
            ]),
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'claim_id' => Claim::factory(),
        ];
    }
}
