<?php

namespace Tests\Feature;

use App\Models\Insurer;
use App\Models\InsurerConfiguration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClaimProcessingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $insurer = Insurer::create(['name' => 'Test Insurer', 'code' => 'INS-A']);
        InsurerConfiguration::create([
            'insurer_code' => 'INS-A',
            'daily_capacity' => 100,
            'min_batch_size' => 5,
            'max_batch_size' => 25,
            'date_preference' => 'encounter',
            'specialty_efficiency' => ['cardiology' => 0.8],
            'priority_multiplier' => 1.00,
            'value_multiplier' => 0.0001,
        ]);
    }

    /** @test */
    public function can_submit_claim_and_create_batch()
    {
        Mail::fake();

        $response = $this->postJson('/api/claims', [
            'provider_name' => 'Test Hospital',
            'insurer_code' => 'INS-A',
            'encounter_date' => '2025-01-15',
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'items' => [
                ['name' => 'Consultation', 'unit_price' => 150.00, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('claims', ['provider_name' => 'Test Hospital']);
        $this->assertDatabaseHas('batches', ['provider_name' => 'Test Hospital']);
    }

    /** @test */
    public function groups_multiple_claims_into_same_batch()
    {
        Mail::fake();

        $claimData = [
            'provider_name' => 'Same Hospital',
            'insurer_code' => 'INS-A',
            'encounter_date' => '2025-01-15',
            'priority_level' => 3,
            'specialty' => 'cardiology',
        ];

        $this->postJson('/api/claims', array_merge($claimData, [
            'items' => [['name' => 'Item 1', 'unit_price' => 100.00, 'quantity' => 1]],
        ]));

        $this->postJson('/api/claims', array_merge($claimData, [
            'items' => [['name' => 'Item 2', 'unit_price' => 200.00, 'quantity' => 1]],
        ]));

        $this->assertDatabaseCount('batches', 1);
        $this->assertDatabaseCount('claims', 2);

        $batch = \App\Models\Batch::first();
        $this->assertEquals(2, $batch->claims_count);
        $this->assertEquals(300.00, $batch->total_amount);
    }

    /** @test */
    public function validates_required_claim_data()
    {
        $response = $this->postJson('/api/claims', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['provider_name', 'insurer_code']);
    }
}
