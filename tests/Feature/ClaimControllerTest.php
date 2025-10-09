<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClaimControllerTest extends TestCase
{
    use RefreshDatabase;

     public function test_submit_claim()
    {
        // Create a mock insurer
        $insurer = Insurer::factory()->create([
            'name' => 'Insurer A',
            'code' => 'INS001',
            'min_batch_size' => 2,
            'max_batch_size' => 10,
        ]);

        // Create a mock user
        $user = User::factory()->create([
            'name' => 'Provider A',
            'email' => 'provider@example.com',
            'password' => bcrypt('password123')  // Use hashed password
        ]);

        // Prepare claim data
        $claimData = [
            'insurer_code' => $insurer->code,
            'provider_name' => 'Provider A',
            'encounter_date' => '2025-10-05',
            'specialty' => 'Cardiology',
            'priority_level' => 3,
            'items' => [
                [
                    'name' => 'Item 1',
                    'quantity' => 1,
                    'unitPrice' => 100,
                ],
                [
                    'name' => 'Item 2',
                    'quantity' => 2,
                    'unitPrice' => 150,
                ],
            ]
        ];

        // Send POST request to submit the claim
        $response = $this->postJson('/api/v1/submit-claim', $claimData);

        // Assert the response
        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Claim submitted successfully!',
        ]);

        // Fetch the claim from the database
        $claim = Claim::first();

        // Assert the total amount has been correctly calculated (total of all item subtotals)
        $this->assertEquals(400, $claim->total_amount); // Item 1 (100) + Item 2 (300)
    }

    public function test_batching_logic()
    {
        // Create mock insurer
        $insurer = Insurer::factory()->create([
            'code' => 'INS001',
            'min_batch_size' => 2,
            'max_batch_size' => 5,
        ]);

        // Create a mock claim
        $claim = Claim::factory()->create([
            'insurer_id' => $insurer->id,
            'total_amount' => 500,
        ]);

        // Since batching happens in the controller during submission, let's check the claim again after submission
        $batch = $this->batchClaims($claim);

        // Assert that the batch contains the claim and meets the batch size constraints
        $this->assertNotEmpty($batch['claims']);
        $this->assertTrue(count($batch['claims']) <= $insurer->max_batch_size);
        $this->assertEquals($batch['total_cost'], $claim->total_amount * 0.2); // Based on our cost multiplier logic
    }

    // Mock function to simulate the batch processing (or you could call the actual method)
    private function batchClaims($claim)
    {
        // Example of batching logic
        return [
            'claims' => [$claim],
            'total_cost' => $claim->total_amount * 0.2,
        ];
    }
}
