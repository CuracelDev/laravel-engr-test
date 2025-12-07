<?php

namespace Tests\Feature;

use App\Helpers\BatchClaimHelper;
use App\Helpers\CalculateProcessingCost;
use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchingAlgorithmTest extends TestCase
{
    use RefreshDatabase;

    protected Insurer $insurer;
    protected BatchClaimHelper $batchClaim;

    protected function setUp(): void
    {
        parent::setUp();

        $this->insurer = Insurer::create([
            'name' => 'Test Insurer',
            'code' => 'TEST-INS',
            'email' => 'test@insurer.com',
            'daily_capacity' => 100,
            'min_batch_size' => 5,
            'max_batch_size' => 10,
            'date_preference' => 'encounter',
            'base_processing_cost' => 100.00,
            'specialty_efficiency' => [
                'cardiology' => 0.8,
                'orthopedics' => 1.2,
                'general' => 1.0,
            ],
        ]);

        $this->batchClaim = app(BatchClaimHelper::class);
    }

    public function test_creates_batch_for_new_claim()
    {
        $claim = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => '2025-12-05',
            'submission_date' => '2025-12-07',
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $batch = $this->batchClaim->execute($claim);

        $this->assertNotNull($batch);
        $this->assertEquals($this->insurer->id, $batch->insurer_id);
        $this->assertEquals('Dr. Smith', $batch->provider_name);
        $this->assertEquals(1, $batch->claim_count);
    }

    public function test_adds_claim_to_existing_batch()
    {
        $batchDate = '2025-12-05';

        $existingBatch = Batch::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'batch_date' => $batchDate,
            'claim_count' => 3,
            'total_amount' => 1000.00,
            'processing_cost' => 300.00,
            'status' => 'pending',
        ]);

        $claim = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => $batchDate,
            'submission_date' => '2025-12-07',
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $batch = $this->batchClaim->execute($claim);

        $this->assertEquals($existingBatch->id, $batch->id);
        $this->assertEquals(4, $batch->fresh()->claim_count);
    }

    public function test_respects_max_batch_size()
    {
        $batchDate = '2025-12-05';

        $existingBatch = Batch::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'batch_date' => $batchDate,
            'claim_count' => 10,
            'total_amount' => 5000.00,
            'processing_cost' => 1500.00,
            'status' => 'pending',
        ]);

        $claim = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => $batchDate,
            'submission_date' => '2025-12-07',
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $batch = $this->batchClaim->execute($claim);

        $this->assertNotEquals($existingBatch->id, $batch->id);
        $this->assertEquals(1, $batch->claim_count);
    }

    public function test_uses_encounter_date_for_batching_when_preferred()
    {
        $encounterDate = '2025-12-05';
        $submissionDate = '2025-12-10';

        $claim = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => $encounterDate,
            'submission_date' => $submissionDate,
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $batch = $this->batchClaim->execute($claim);

        $this->assertEquals($encounterDate, $batch->batch_date->format('Y-m-d'));
    }

    public function test_uses_submission_date_for_batching_when_preferred()
    {
        $this->insurer->update(['date_preference' => 'submission']);

        $encounterDate = '2025-12-05';
        $submissionDate = '2025-12-10';

        $claim = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => $encounterDate,
            'submission_date' => $submissionDate,
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $batch = $this->batchClaim->execute($claim);

        $this->assertLessThanOrEqual(
            Carbon::parse($submissionDate)->format('Y-m-d'),
            $batch->batch_date->format('Y-m-d')
        );
    }

    public function test_calculates_processing_cost_with_time_multiplier()
    {
        $calculateCost = app(CalculateProcessingCost::class);

        $claim = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => '2025-12-05',
            'submission_date' => '2025-12-07',
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $earlyMonthCost = $calculateCost->execute(
            $this->insurer,
            $claim,
            Carbon::parse('2025-12-05')
        );

        $lateMonthCost = $calculateCost->execute(
            $this->insurer,
            $claim,
            Carbon::parse('2025-12-25')
        );

        $this->assertGreaterThan($earlyMonthCost, $lateMonthCost);
    }

    public function test_calculates_processing_cost_with_specialty_efficiency()
    {
        $calculateCost = app(CalculateProcessingCost::class);

        $cardiologyClaim = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => '2025-12-05',
            'submission_date' => '2025-12-07',
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $orthopedicsClaim = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => '2025-12-05',
            'submission_date' => '2025-12-07',
            'priority_level' => 3,
            'specialty' => 'orthopedics',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $cardiologyCost = $calculateCost->execute(
            $this->insurer,
            $cardiologyClaim,
            Carbon::parse('2025-12-05')
        );

        $orthopedicsCost = $calculateCost->execute(
            $this->insurer,
            $orthopedicsClaim,
            Carbon::parse('2025-12-05')
        );

        $this->assertLessThan($orthopedicsCost, $cardiologyCost);
    }

    public function test_calculates_processing_cost_with_priority_multiplier()
    {
        $calculateCost = app(CalculateProcessingCost::class);

        $lowPriority = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => '2025-12-05',
            'submission_date' => '2025-12-07',
            'priority_level' => 1,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $highPriority = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => '2025-12-05',
            'submission_date' => '2025-12-07',
            'priority_level' => 5,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $lowPriorityCost = $calculateCost->execute(
            $this->insurer,
            $lowPriority,
            Carbon::parse('2025-12-05')
        );

        $highPriorityCost = $calculateCost->execute(
            $this->insurer,
            $highPriority,
            Carbon::parse('2025-12-05')
        );

        $this->assertLessThan($highPriorityCost, $lowPriorityCost);
    }

    public function test_optimizes_batch_date_for_cost_reduction()
    {
        $claim = Claim::create([
            'insurer_id' => $this->insurer->id,
            'provider_name' => 'Dr. Smith',
            'encounter_date' => '2025-12-20',
            'submission_date' => '2025-12-20',
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $batch = $this->batchClaim->execute($claim);

        $this->assertLessThanOrEqual(
            Carbon::parse('2025-12-20')->format('Y-m-d'),
            $batch->batch_date->format('Y-m-d')
        );
    }
}
