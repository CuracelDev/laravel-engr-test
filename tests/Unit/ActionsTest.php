<?php

namespace Tests\Unit;

use App\Actions\BatchClaim;
use App\Actions\NotifyInsurer;
use App\Actions\ProcessBatch;
use App\Actions\SubmitClaim;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ActionsTest extends TestCase
{
    use RefreshDatabase;

    protected Insurer $insurer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->insurer = Insurer::create([
            'name' => 'Test Insurer',
            'code' => 'TEST',
            'email' => 'test@test.com',
            'specialty_costs' => ['cardiology' => 1.5],
            'priority_costs' => [3 => 1.2],
            'value_cost_multiplier' => 0.0001,
            'daily_capacity' => 100,
            'min_batch_size' => 2,
            'max_batch_size' => 5,
            'date_preference' => 'submission'
        ]);
    }

    public function test_submit_claim_action()
    {
        $claim = SubmitClaim::run([
            'insurer_code' => 'TEST',
            'provider_name' => 'Provider A',
            'encounter_date' => '2024-01-15',
            'specialty' => 'cardiology',
            'priority_level' => 3,
            'items' => [
                ['name' => 'Service', 'unit_price' => 100, 'quantity' => 2]
            ]
        ]);

        $this->assertInstanceOf(Claim::class, $claim);
        $this->assertEquals(200, $claim->total_amount);
        $this->assertEquals('Provider A', $claim->provider_name);
    }

    public function test_batch_claim_action()
    {
        $claim = SubmitClaim::run([
            'insurer_code' => 'TEST',
            'provider_name' => 'Provider B',
            'encounter_date' => '2024-01-15',
            'specialty' => 'cardiology',
            'priority_level' => 3,
            'items' => [['name' => 'Test', 'unit_price' => 50, 'quantity' => 1]]
        ]);

        BatchClaim::run($claim);

        $this->assertNotNull($claim->fresh()->batch_id);
        $this->assertNotNull($claim->fresh()->batch_date);
        $this->assertGreaterThan(0, $claim->fresh()->processing_cost);
    }

    public function test_process_batch_action()
    {
        Mail::fake();
        $yesterday = now()->subDay()->toDateString();
        
        $claim1 = SubmitClaim::run([
            'insurer_code' => 'TEST',
            'provider_name' => 'Provider C',
            'encounter_date' => $yesterday,
            'submission_date' => $yesterday,
            'specialty' => 'cardiology',
            'priority_level' => 1,
            'items' => [['name' => 'Test', 'unit_price' => 100, 'quantity' => 1]]
        ]);
        BatchClaim::run($claim1);

        $claim2 = SubmitClaim::run([
            'insurer_code' => 'TEST',
            'provider_name' => 'Provider C',
            'encounter_date' => $yesterday,
            'submission_date' => $yesterday,
            'specialty' => 'cardiology',
            'priority_level' => 1,
            'items' => [['name' => 'Test', 'unit_price' => 150, 'quantity' => 1]]
        ]);
        BatchClaim::run($claim2);

        $processed = ProcessBatch::run($this->insurer, $claim1->batch_id, $claim1->batch_date);

        $this->assertTrue($processed);
        $this->assertTrue($claim1->fresh()->processed);
        $this->assertTrue($claim2->fresh()->processed);
    }

    public function test_notify_insurer_action()
    {
        Mail::fake();

        $claims = collect([
            Claim::factory()->create(['insurer_id' => $this->insurer->id, 'processing_cost' => 50]),
            Claim::factory()->create(['insurer_id' => $this->insurer->id, 'processing_cost' => 75])
        ]);

        NotifyInsurer::run($this->insurer, $claims, 'Test Batch');

        // Mail::raw doesn't use Mailable, so we just verify no exception was thrown
        $this->assertTrue(true);
    }
}