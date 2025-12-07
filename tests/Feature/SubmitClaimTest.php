<?php

namespace Tests\Feature;

use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SubmitClaimTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Insurer::create([
            'name' => 'Test Insurer',
            'code' => 'TEST-INS',
            'email' => 'test@insurer.com',
            'daily_capacity' => 100,
            'min_batch_size' => 5,
            'max_batch_size' => 50,
            'date_preference' => 'encounter',
            'base_processing_cost' => 100.00,
            'specialty_efficiency' => [
                'cardiology' => 1.0,
                'general' => 1.0,
            ],
        ]);
    }

    public function test_can_submit_claim_successfully()
    {
        Mail::fake();

        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Dr. John Smith',
            'encounter_date' => '2025-12-01',
            'specialty' => 'cardiology',
            'priority_level' => 3,
            'items' => [
                [
                    'name' => 'Consultation',
                    'unit_price' => 150.00,
                    'quantity' => 1,
                ],
                [
                    'name' => 'Lab Test',
                    'unit_price' => 75.50,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Claim submitted and batched successfully',
            ])
            ->assertJsonStructure([
                'data' => [
                    'claim_id',
                    'status',
                    'batch_id',
                    'total_amount',
                ],
            ]);

        $this->assertDatabaseHas('claims', [
            'provider_name' => 'Dr. John Smith',
            'specialty' => 'cardiology',
            'status' => 'batched',
        ]);

        $this->assertDatabaseHas('claim_items', [
            'name' => 'Consultation',
            'unit_price' => 150.00,
        ]);
    }

    public function test_validates_required_fields()
    {
        $response = $this->postJson('/api/claims', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'insurer_code',
                'provider_name',
                'encounter_date',
                'specialty',
                'items',
            ]);
    }

    public function test_validates_insurer_code_exists()
    {
        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'INVALID-CODE',
            'provider_name' => 'Dr. John Smith',
            'encounter_date' => '2025-12-01',
            'specialty' => 'cardiology',
            'items' => [
                [
                    'name' => 'Consultation',
                    'unit_price' => 150.00,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['insurer_code']);
    }

    public function test_validates_encounter_date_not_in_future()
    {
        $futureDate = now()->addDays(5)->format('Y-m-d');

        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Dr. John Smith',
            'encounter_date' => $futureDate,
            'specialty' => 'cardiology',
            'items' => [
                [
                    'name' => 'Consultation',
                    'unit_price' => 150.00,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['encounter_date']);
    }

    public function test_validates_items_array_not_empty()
    {
        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Dr. John Smith',
            'encounter_date' => '2025-12-01',
            'specialty' => 'cardiology',
            'items' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    public function test_calculates_total_amount_correctly()
    {
        Mail::fake();

        $response = $this->postJson('/api/claims', [
            'insurer_code' => 'TEST-INS',
            'provider_name' => 'Dr. John Smith',
            'encounter_date' => '2025-12-01',
            'specialty' => 'cardiology',
            'priority_level' => 3,
            'items' => [
                [
                    'name' => 'Consultation',
                    'unit_price' => 100.00,
                    'quantity' => 2,
                ],
                [
                    'name' => 'Lab Test',
                    'unit_price' => 50.00,
                    'quantity' => 3,
                ],
            ],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('claims', [
            'provider_name' => 'Dr. John Smith',
            'total_amount' => 350.00,
        ]);
    }
}
