<?php

namespace Tests\Feature;

use App\Actions\SubmitClaim;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClaimSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Insurer::create([
            'name' => 'Test Insurer',
            'code' => 'TEST-INS',
            'email' => 'test@insurer.com',
            'specialty_costs' => ['cardiology' => 1.2, 'general' => 1.0],
            'priority_costs' => [1 => 1.0, 2 => 1.1, 3 => 1.3],
            'value_cost_multiplier' => 0.0001,
            'daily_capacity' => 100,
            'min_batch_size' => 2,
            'max_batch_size' => 10,
            'date_preference' => 'submission'
        ]);
    }

    public function test_claim_submission_creates_claim_successfully()
    {
        $claimData = [
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Test Provider',
            'encounter_date' => '2024-01-15',
            'specialty' => 'cardiology',
            'priority_level' => 2,
            'items' => [
                ['name' => 'Consultation', 'unit_price' => 100, 'quantity' => 1],
                ['name' => 'X-Ray', 'unit_price' => 50, 'quantity' => 2]
            ]
        ];

        $response = $this->postJson('/api/claims', $claimData);

        $response->assertStatus(200)
                ->assertJsonStructure(['message', 'claim_id', 'batch_id']);

        $this->assertDatabaseHas('claims', [
            'provider_name' => 'Test Provider',
            'specialty' => 'cardiology',
            'priority_level' => 2,
            'total_amount' => 200.00
        ]);
    }

    public function test_claim_validation_fails_with_invalid_data()
    {
        $this->expectException(\Exception::class);
        
        \App\Actions\SubmitClaim::run([
            'insurer_code' => 'INVALID',
            'provider_name' => '',
            'encounter_date' => 'invalid-date',
            'specialty' => 'invalid-specialty',
            'priority_level' => 10,
            'items' => []
        ]);
    }

    public function test_batch_processing_with_minimum_batch_size()
    {
        Mail::fake();
        $insurer = Insurer::where('code', 'TEST-INS')->first();
        $yesterday = now()->subDay()->toDateString();
        
        $claim1 = \App\Actions\SubmitClaim::run([
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Provider A',
            'encounter_date' => $yesterday,
            'submission_date' => $yesterday,
            'specialty' => 'general',
            'priority_level' => 1,
            'items' => [['name' => 'Service', 'unit_price' => 100, 'quantity' => 1]]
        ]);
        \App\Actions\BatchClaim::run($claim1);

        $claim2 = \App\Actions\SubmitClaim::run([
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Provider A',
            'encounter_date' => $yesterday,
            'submission_date' => $yesterday,
            'specialty' => 'general',
            'priority_level' => 1,
            'items' => [['name' => 'Service', 'unit_price' => 150, 'quantity' => 1]]
        ]);
        \App\Actions\BatchClaim::run($claim2);
        
        \App\Actions\ProcessBatch::run($insurer, $claim1->batch_id, $claim1->batch_date);

        $this->assertEquals(2, Claim::where('processed', true)->count());
    }

    public function test_processing_cost_calculation()
    {
        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Test Provider',
            'encounter_date' => '2024-01-15',
            'specialty' => 'cardiology',
            'priority_level' => 3,
            'items' => [['name' => 'Surgery', 'unit_price' => 1000, 'quantity' => 1]]
        ]);

        $claim = Claim::latest()->first();
        
        $this->assertNotNull($claim->processing_cost);
        $this->assertGreaterThan(0, $claim->processing_cost);
        
        // Cost should reflect specialty multiplier (1.2) and priority multiplier (1.3)
        $this->assertGreaterThan(20, $claim->processing_cost); // Base cost is at least 20%
    }
}