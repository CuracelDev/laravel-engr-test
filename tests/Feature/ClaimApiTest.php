<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Insurer;
use Database\Seeders\InsurerSeeder;

class ClaimApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_claim_can_be_submitted_with_items()
    {
        $this->seed(InsurerSeeder::class);
        $insurer = Insurer::where('code', 'INS-A')->firstOrFail();

        $payload = [
            'insurer_code' => $insurer->code,
            'provider_name' => 'Test Provider',
            'encounter_date' => '2025-10-10',
            'specialty' => 'Cardiology',
            'priority_level' => 3,
            'items' => [
                ['name' => 'Test Item 1', 'unit_price' => 100, 'quantity' => 2],
                ['name' => 'Test Item 2', 'unit_price' => 200, 'quantity' => 1],
            ]
        ];

        $response = $this->postJson('/api/claims', $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'provider_name',
                'items' => [
                    ['id', 'name', 'unit_price', 'quantity', 'subtotal']
                ]
            ]
        ]);

        $this->assertDatabaseHas('claims', ['provider_name' => 'Test Provider']);
        $this->assertDatabaseCount('claim_items', 2);
    }

}
