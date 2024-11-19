<?php

namespace Tests\Feature;

use App\Models\Insurer;
use App\Models\Specialty;
use Database\Seeders\ClaimSeeder;
use Database\Seeders\InsurerSeeder;
use Database\Seeders\SpecialitiesTableSeeder;
use Database\Seeders\SpecialtyEfficiencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClaimTest extends TestCase
{
    use RefreshDatabase;
       /**
     * Test a successful claim submission.
     */
    public function test_successful_claim_submission()
    {
        $this->seed([
            InsurerSeeder::class, 
            SpecialitiesTableSeeder::class,
            SpecialtyEfficiencySeeder::class,
            ClaimSeeder::class
        ]);

        $insurer = Insurer::factory()->create();
        // $specialty = ;
        // $specialty = Specialty::factory()->create();
        $specialty = Specialty::orderBy('id','DESC')->first();
        $level = rand(1,3);
        $payload = [
            'provider_name' => $insurer->name,
            'insurer_code' => $insurer->code,
            'encounter_date' => '2024-11-18',
            'priority_level' => "$level",
            'specialty' => $specialty->id,
            'encounter_date' => '2024-11-18',
            'items' => [
                ['name' => 'X-ray', 'unit_price' => 100, 'qty' => 2],
                ['name' => 'Blood Test', 'unit_price' => 50, 'qty' => 1],
            ],
        ];

        $response = $this->postJson('/api/claims', $payload);

        $response->assertStatus(200);

        // Check if the claim was saved in the database
        $this->assertDatabaseHas('claims', [
            'provider_name' => 'Abiola Healthcare',
            'insurer_code' => $insurer->id,
            'encounter_date' => '2024-11-18',
        ]);

        // Check if items were saved
        $this->assertDatabaseHas('items', [
            'name' => 'X-ray',
            'unit_price' => 100,
            'qty' => 2,
        ]);
        $this->assertDatabaseHas('items', [
            'name' => 'Blood Test',
            'unit_price' => 50,
            'qty' => 1,
        ]);
    }

    /**
     * Test validation failure on claim submission.
     */
    public function test_claim_submission_validation_failure()
    {
        $insurer = Insurer::factory()->create();
        $payload = [
            // Missing providerName
            'insurer_code' => $insurer->insurer_code,
            'encounter_date' => '2024-11-18',
            'items' => [
                ['name' => 'X-ray', 'unit_price' => 100, 'qty' => 2],
            ],
        ];

        $response = $this->postJson('/api/claims', $payload);

        $response->assertStatus(422) // Unprocessable Entity
            ->assertJsonValidationErrors(['provider_name']);
    }

    /**
     * Test failure when no items are submitted.
     */
    public function test_claim_submission_no_items()
    {
        $payload = [
            'provider_name' => 'ABC Healthcare',
            'insurer_code' => 'INS123',
            'encounter_date' => '2024-11-18',
            'items' => [], // No items
        ];

        $response = $this->postJson('/api/claims', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }
}
