<?php

namespace Tests\Unit;

use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchingAlgorithmTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_id_generation()
    {
        $insurer = Insurer::create([
            'name' => 'Test Insurer',
            'code' => 'TEST',
            'email' => 'test@test.com',
            'date_preference' => 'submission'
        ]);

        $claim = new Claim([
            'provider_name' => 'Provider A',
            'insurer_id' => $insurer->id,
            'encounter_date' => '2024-01-15',
            'submission_date' => '2024-01-16',
        ]);

        $claim->insurer = $insurer;
        
        $batchId = $claim->generateBatchId();
        
        $this->assertEquals('Provider A Jan 16 2024', $batchId);
    }

    public function test_encounter_date_preference()
    {
        $insurer = Insurer::create([
            'name' => 'Test Insurer',
            'code' => 'TEST',
            'email' => 'test@test.com',
            'date_preference' => 'encounter'
        ]);

        $claim = new Claim([
            'provider_name' => 'Provider B',
            'insurer_id' => $insurer->id,
            'encounter_date' => '2024-01-10',
            'submission_date' => '2024-01-15',
        ]);

        $claim->insurer = $insurer;
        
        $batchDate = $claim->getBatchDateForInsurer();
        
        $this->assertEquals('2024-01-10', $batchDate->format('Y-m-d'));
    }

    public function test_processing_cost_calculation_with_time_factor()
    {
        $insurer = Insurer::create([
            'name' => 'Test Insurer',
            'code' => 'TEST',
            'email' => 'test@test.com',
            'specialty_costs' => ['cardiology' => 1.5],
            'priority_costs' => [3 => 1.2],
            'value_cost_multiplier' => 0.0001
        ]);

        $claim = new Claim([
            'specialty' => 'cardiology',
            'priority_level' => 3,
            'total_amount' => 1000
        ]);

        // Test cost on 1st of month (20% base)
        $cost1 = $insurer->calculateProcessingCost($claim, 1);
        
        // Test cost on 30th of month (50% base)
        $cost30 = $insurer->calculateProcessingCost($claim, 30);
        
        $this->assertGreaterThan($cost1, $cost30);
        $this->assertGreaterThan(20, $cost1); // Should be > 20 due to multipliers
    }

    public function test_batch_size_constraints()
    {
        $insurer = Insurer::create([
            'name' => 'Test Insurer',
            'code' => 'TEST',
            'email' => 'test@test.com',
            'min_batch_size' => 3,
            'max_batch_size' => 5
        ]);

        $this->assertEquals(3, $insurer->min_batch_size);
        $this->assertEquals(5, $insurer->max_batch_size);
    }
}