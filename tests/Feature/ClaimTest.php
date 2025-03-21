<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ClaimTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_submit_valid_claim(): void
    {
        $this->seed(\Database\Seeders\InsurerSeeder::class);
        $insurer = Insurer::first();

        $claimData = [
            'insurer_code' => $insurer->code,
            'provider_name' => 'Provider A',
            'encounter_date' => '2023-10-15',
            'items' => [
                [
                    'name' => 'Item 1',
                    'unit_price' => 100,
                    'quantity' => 2,
                ],
                [
                    'name' => 'Item 2',
                    'unit_price' => 50,
                    'quantity' => 3,
                ],
            ],
            'specialty' => 'cardiology',
            'priority_level' => 3,
        ];

        $response = $this->postJson('/api/add-claims', $claimData);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Claim submitted and batched successfully!',
        ]);

        $this->assertDatabaseHas('claims', [
            'insurer_code' => $insurer->code,
            'provider_name' => 'Provider A',
            'encounter_date' => '2023-10-15',
            'specialty' => 'cardiology',
            'priority_level' => 3,
        ]);

        $claim = Claim::first();
        $this->assertDatabaseHas('claim_items', [
            'claim_id' => $claim->id,
            'item_name' => 'Item 1',
            'unit_price' => 100,
            'quantity' => 2,
            'subtotal' => 200,
        ]);
        $this->assertDatabaseHas('claim_items', [
            'claim_id' => $claim->id,
            'item_name' => 'Item 2',
            'unit_price' => 50,
            'quantity' => 3,
            'subtotal' => 150,
        ]);
    }
   
}