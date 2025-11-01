<?php

namespace Tests\Feature;

use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmitClaimTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed(\Database\Seeders\InsurerSeeder::class);
    }

    public function test_submit_claim_creates_items_and_batches(): void
    {
        $insurer = Insurer::firstOrFail();

        $payload = [
            'insurer_code' => $insurer->code,
            'provider_name' => 'Provider A',
            'encounter_date' => '2025-10-15',
            'submission_date' => '2025-10-16',
            'specialty' => 'cardiology',
            'priority_level' => 3,
            'items' => [
                ['name'=>'ECG','unit_price'=>2500,'quantity'=>1],
                ['name'=>'Consultation','unit_price'=>15000,'quantity'=>1],
            ],
        ];

        $res = $this->postJson('/api/claims', $payload);
        $res->assertCreated()
            ->assertJsonPath('claim.items_count', 2)
            ->assertJsonPath('claim.total_value', '17500.00')
            ->assertJsonStructure(['claim' => ['batch' => ['batch_code']]]);

        $data = $res->json('claim');
        $this->assertEquals('ECG', $data['items'][0]['name']);
        $this->assertEquals('Consultation', $data['items'][1]['name']);
    }
}