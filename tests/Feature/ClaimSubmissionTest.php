<?php

namespace Tests\Feature;

use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed an insurer for testing
        Insurer::create([
            'name' => 'Test Insurer',
            'code' => 'TEST-INS',
            'base_processing_cost' => 10.00,
            'daily_capacity' => 100,
            'min_batch_size' => 1,
            'max_batch_size' => 50,
            'date_preference' => 'encounter',
            'specialty_efficiency_multipliers' => [
                'cardiology' => 1.0,
                'orthopedics' => 1.0,
                'neurology' => 1.0,
                'pediatrics' => 1.0,
                'oncology' => 1.0,
                'dermatology' => 1.0,
                'radiology' => 1.0,
                'psychiatry' => 1.0,
                'gastroenterology' => 1.0,
                'endocrinology' => 1.0,
            ],
        ]);
    }

    public function test_can_submit_claim_with_valid_data()
    {
        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Dr. Test Provider',
            'encounter_date' => '2024-01-15',
            'priority_level' => 2,
            'specialty' => 'cardiology',
            'items' => [
                [
                    'name' => 'Consultation',
                    'unit_price' => 100.00,
                    'quantity' => 1,
                ],
                [
                    'name' => 'X-Ray',
                    'unit_price' => 50.00,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('claims', [
            'provider_name' => 'Dr. Test Provider',
            'specialty' => 'cardiology',
            'priority_level' => 2,
            'total_amount' => 200.00,
        ]);

        $this->assertDatabaseCount('claim_items', 2);
    }

    public function test_claim_submission_requires_insurer_code()
    {
        $response = $this->postJson('/api/claims', [
            'provider_name' => 'Dr. Test Provider',
            'encounter_date' => '2024-01-15',
            'priority_level' => 2,
            'specialty' => 'cardiology',
            'items' => [
                [
                    'name' => 'Consultation',
                    'unit_price' => 100.00,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('insurer_code');
    }

    public function test_claim_submission_validates_insurer_code_exists()
    {
        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'INVALID-CODE',
            'provider_name' => 'Dr. Test Provider',
            'encounter_date' => '2024-01-15',
            'priority_level' => 2,
            'specialty' => 'cardiology',
            'items' => [
                [
                    'name' => 'Consultation',
                    'unit_price' => 100.00,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('insurer_code');
    }

    public function test_claim_submission_validates_priority_level()
    {
        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Dr. Test Provider',
            'encounter_date' => '2024-01-15',
            'priority_level' => 10, // Invalid: must be 1-5
            'specialty' => 'cardiology',
            'items' => [
                [
                    'name' => 'Consultation',
                    'unit_price' => 100.00,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('priority_level');
    }

    public function test_claim_submission_validates_specialty()
    {
        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Dr. Test Provider',
            'encounter_date' => '2024-01-15',
            'priority_level' => 2,
            'specialty' => 'invalid_specialty',
            'items' => [
                [
                    'name' => 'Consultation',
                    'unit_price' => 100.00,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('specialty');
    }

    public function test_claim_submission_requires_at_least_one_item()
    {
        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Dr. Test Provider',
            'encounter_date' => '2024-01-15',
            'priority_level' => 2,
            'specialty' => 'cardiology',
            'items' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('items');
    }

    public function test_can_retrieve_batches()
    {
        $response = $this->getJson('/api/batches');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }
}

