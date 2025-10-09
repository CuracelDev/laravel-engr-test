<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClaimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Create a sample claim
        $insurer = Insurer::first();  // Get the first insurer from the database
        $user = User::first();  // Get the first user

        $claim = Claim::create([
            'insurer_id' => $insurer->id,
            'provider_name' => 'Provider A',
            'encounter_date' => '2025-10-05',
            'specialty' => 'Cardiology',
            'priority_level' => 3,
            'total_amount' => 500,  // Just an example amount
        ]);

        // Create claim items
        $claim->items()->createMany([
            [
                'name' => 'Item 1',
                'quantity' => 1,
                'unit_price' => 100,
                'subtotal' => 100,
            ],
            [
                'name' => 'Item 2',
                'quantity' => 2,
                'unit_price' => 150,
                'subtotal' => 300,
            ],
        ]);
    }
}
