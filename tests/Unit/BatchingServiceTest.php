<?php

namespace Tests\Unit;

use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use App\Models\InsurerConfiguration;
use App\Services\ClaimBatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ClaimBatchingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClaimBatchingService();

        $insurer = Insurer::create(['name' => 'Test Insurer', 'code' => 'INS-A']);
        InsurerConfiguration::create([
            'insurer_code' => 'INS-A',
            'daily_capacity' => 100,
            'min_batch_size' => 5,
            'max_batch_size' => 25,
            'date_preference' => 'encounter',
            'specialty_efficiency' => ['cardiology' => 0.8, 'orthopedics' => 1.2],
            'priority_multiplier' => 1.00,
            'value_multiplier' => 0.0001,
        ]);
    }

    /** @test */
    public function creates_new_batch_for_first_claim()
    {
        Mail::fake();

        $claim = Claim::create([
            'provider_name' => 'Test Provider',
            'insurer_code' => 'INS-A',
            'encounter_date' => '2025-01-15',
            'submission_date' => '2025-01-15',
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 1000.00,
            'status' => 'pending',
        ]);

        $batch = $this->service->processClaim($claim);

        $this->assertInstanceOf(Batch::class, $batch);
        $this->assertEquals('Test Provider', $batch->provider_name);
        $this->assertEquals(1, $batch->claims_count);
        $this->assertGreaterThan(0, $batch->processing_cost);
    }

    /** @test */
    public function calculates_processing_cost_with_all_factors()
    {
        Mail::fake();

        $cardiologyClaim = Claim::create([
            'provider_name' => 'Provider A',
            'insurer_code' => 'INS-A',
            'encounter_date' => '2025-01-01',
            'submission_date' => '2025-01-01',
            'priority_level' => 1,
            'specialty' => 'cardiology',
            'total_amount' => 1000.00,
            'status' => 'pending',
        ]);

        $orthopedicsClaim = Claim::create([
            'provider_name' => 'Provider B',
            'insurer_code' => 'INS-A',
            'encounter_date' => '2025-01-30',
            'submission_date' => '2025-01-30',
            'priority_level' => 5,
            'specialty' => 'orthopedics',
            'total_amount' => 1000.00,
            'status' => 'pending',
        ]);

        $cardiologyBatch = $this->service->processClaim($cardiologyClaim);
        $orthopedicsBatch = $this->service->processClaim($orthopedicsClaim);

        $this->assertGreaterThan(
            $cardiologyBatch->processing_cost,
            $orthopedicsBatch->processing_cost
        );
    }

    /** @test */
    public function adds_claim_to_existing_batch_when_appropriate()
    {
        Mail::fake();

        $firstClaim = Claim::create([
            'provider_name' => 'Same Provider',
            'insurer_code' => 'INS-A',
            'encounter_date' => '2025-01-15',
            'submission_date' => '2025-01-15',
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 500.00,
            'status' => 'pending',
        ]);

        $firstBatch = $this->service->processClaim($firstClaim);

        $secondClaim = Claim::create([
            'provider_name' => 'Same Provider',
            'insurer_code' => 'INS-A',
            'encounter_date' => '2025-01-15',
            'submission_date' => '2025-01-15',
            'priority_level' => 3,
            'specialty' => 'cardiology',
            'total_amount' => 300.00,
            'status' => 'pending',
        ]);

        $secondBatch = $this->service->processClaim($secondClaim);

        $this->assertEquals($firstBatch->id, $secondBatch->id);

        $firstBatch->refresh();
        $this->assertEquals(2, $firstBatch->claims_count);
        $this->assertEquals(800.00, $firstBatch->total_amount);
    }
}
