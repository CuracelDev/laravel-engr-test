<?php

namespace Tests\Feature;

use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_claim_via_api()
    {
        $insurer = Insurer::factory()->create(['code' => 'TEST01']);

        $payload = [
            'provider_name' => 'Dr. Feature',
            'insurer_code' => 'TEST01',
            'encounter_date' => '2024-01-01',
            'specialty' => 'Cardiology',
            'priority' => 3,
            'items' => [
                ['name' => 'Item 1', 'unit_price' => 100, 'quantity' => 2],
                ['name' => 'Item 2', 'unit_price' => 50, 'quantity' => 1],
            ]
        ];

        $response = $this->postJson('/api/claims', $payload);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('claims', [
            'provider_name' => 'Dr. Feature',
            'amount' => 250, // (100*2) + (50*1)
            'status' => 'pending',
        ]);

        $this->assertDatabaseCount('claim_items', 2);
    }

    public function test_validates_claim_data()
    {
        $response = $this->postJson('/api/claims', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['provider_name', 'insurer_code', 'items']);
    }
}
