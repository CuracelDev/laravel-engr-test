<?php

namespace Tests\Feature;

use App\Models\Insurer;
use App\Models\Batch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed(\Database\Seeders\InsurerSeeder::class);
    }

    public function test_happy_path_creates_claim_items_and_batches(): void
    {
        $ins = Insurer::firstOrFail();

        $payload = [
            'insurer_code'   => $ins->code,
            'provider_name'  => 'Provider A',
            'encounter_date' => '2025-10-15',
            'submission_date'=> '2025-10-16',
            'specialty'      => 'cardiology',
            'priority_level' => 3,
            'items' => [
                ['name'=>'ECG','unit_price'=>2500,'quantity'=>1],
                ['name'=>'Consultation','unit_price'=>15000,'quantity'=>1],
            ]
        ];

        $res = $this->postJson('/api/claims', $payload);
        $res->assertCreated()
            ->assertJsonPath('claim.items_count', 2)
            ->assertJsonPath('claim.total_value', '17500.00');

        $this->assertStringEndsWith(
            '#1',
            $res->json('claim.batch.batch_code'),
            'First batch for given provider/date should end with #1'
        );
    }

    public function test_claims_reuse_batch_until_max_size_then_roll_over(): void
    {
        $ins = Insurer::firstOrFail();
        $ins->update(['max_batch_size' => 2]);

        $base = [
            'insurer_code'   => $ins->code,
            'provider_name'  => 'Provider Z',
            'encounter_date' => '2025-10-10',
            'submission_date'=> '2025-10-10',
            'specialty'      => 'general',
            'priority_level' => 3,
            'items' => [['name'=>'X','unit_price'=>1000,'quantity'=>1]],
        ];

        $r1 = $this->postJson('/api/claims', $base);
        $r2 = $this->postJson('/api/claims', $base);
        $r3 = $this->postJson('/api/claims', $base);

        $b1 = $r1->json('claim.batch.id');
        $b2 = $r2->json('claim.batch.id');
        $b3 = $r3->json('claim.batch.id');

        $this->assertEquals($b1, $b2, 'First two claims should reuse the same batch');
        $this->assertNotEquals($b1, $b3, 'Third claim should roll over to a new batch');

        $this->assertStringEndsWith('#1', $r1->json('claim.batch.batch_code'));
        $this->assertStringEndsWith('#1', $r2->json('claim.batch.batch_code'));
        $this->assertStringEndsWith('#2', $r3->json('claim.batch.batch_code'));

        $counts = Batch::where('provider_name','Provider Z')->pluck('claims_count')->toArray();
        sort($counts);
        $this->assertEquals([1, 2], $counts);
    }

    public function test_daily_capacity_guard_blocks_when_exceeded(): void
    {
        $ins = Insurer::firstOrFail();
        $ins->update(['daily_capacity' => 1]);

        $payload = [
            'insurer_code'   => $ins->code,
            'provider_name'  => 'Provider C',
            'encounter_date' => now()->toDateString(),
            'submission_date'=> now()->toDateString(),
            'specialty'      => 'general',
            'priority_level' => 3,
            'items' => [['name'=>'X','unit_price'=>1000,'quantity'=>1]],
        ];

        $this->postJson('/api/claims', $payload)->assertCreated();

        $this->postJson('/api/claims', $payload)->assertStatus(422);
    }
}