<?php

namespace Tests\Unit;

use App\Models\Claim;
use App\Models\Insurer;
use App\Services\ProcessingCostCalculator;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessingCostCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected ProcessingCostCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new ProcessingCostCalculator();
    }

    public function test_calculates_base_cost_correctly()
    {
        $insurer = Insurer::factory()->create([
            'base_processing_cost' => 10.00,
            'specialty_efficiency_multipliers' => ['cardiology' => 1.0],
        ]);

        $claim = Claim::factory()->create([
            'insurer_id' => $insurer->id,
            'total_amount' => 0,
            'priority_level' => 1,
            'specialty' => 'cardiology',
        ]);

        // First day of month (multiplier = 0.20)
        $date = Carbon::create(2024, 1, 1);
        
        // Expected: (10 + 0) * 1 * 1.0 * 0.20 = 2.00
        $cost = $this->calculator->calculateCost($claim, $insurer, $date);
        
        $this->assertEquals(2.00, $cost);
    }

    public function test_includes_monetary_value_in_cost()
    {
        $insurer = Insurer::factory()->create([
            'base_processing_cost' => 10.00,
            'specialty_efficiency_multipliers' => ['cardiology' => 1.0],
        ]);

        $claim = Claim::factory()->create([
            'insurer_id' => $insurer->id,
            'total_amount' => 1000.00,
            'priority_level' => 1,
            'specialty' => 'cardiology',
        ]);

        $date = Carbon::create(2024, 1, 1);
        
        // Expected: (10 + (1000/1000 * 10)) * 1 * 1.0 * 0.20 = (10 + 10) * 0.20 = 4.00
        $cost = $this->calculator->calculateCost($claim, $insurer, $date);
        
        $this->assertEquals(4.00, $cost);
    }

    public function test_applies_priority_multiplier()
    {
        $insurer = Insurer::factory()->create([
            'base_processing_cost' => 10.00,
            'specialty_efficiency_multipliers' => ['cardiology' => 1.0],
        ]);

        $claim = Claim::factory()->create([
            'insurer_id' => $insurer->id,
            'total_amount' => 0,
            'priority_level' => 3,
            'specialty' => 'cardiology',
        ]);

        $date = Carbon::create(2024, 1, 1);
        
        // Expected: (10 + 0) * 3 * 1.0 * 0.20 = 6.00
        $cost = $this->calculator->calculateCost($claim, $insurer, $date);
        
        $this->assertEquals(6.00, $cost);
    }

    public function test_applies_specialty_multiplier()
    {
        $insurer = Insurer::factory()->create([
            'base_processing_cost' => 10.00,
            'specialty_efficiency_multipliers' => ['cardiology' => 0.5],
        ]);

        $claim = Claim::factory()->create([
            'insurer_id' => $insurer->id,
            'total_amount' => 0,
            'priority_level' => 1,
            'specialty' => 'cardiology',
        ]);

        $date = Carbon::create(2024, 1, 1);
        
        // Expected: (10 + 0) * 1 * 0.5 * 0.20 = 1.00
        $cost = $this->calculator->calculateCost($claim, $insurer, $date);
        
        $this->assertEquals(1.00, $cost);
    }

    public function test_time_of_month_multiplier_increases()
    {
        $insurer = Insurer::factory()->create([
            'base_processing_cost' => 10.00,
            'specialty_efficiency_multipliers' => ['cardiology' => 1.0],
        ]);

        $claim = Claim::factory()->create([
            'insurer_id' => $insurer->id,
            'total_amount' => 0,
            'priority_level' => 1,
            'specialty' => 'cardiology',
        ]);

        // Test first day
        $firstDay = Carbon::create(2024, 1, 1);
        $costFirstDay = $this->calculator->calculateCost($claim, $insurer, $firstDay);
        
        // Test last day
        $lastDay = Carbon::create(2024, 1, 31);
        $costLastDay = $this->calculator->calculateCost($claim, $insurer, $lastDay);
        
        // Cost should increase from first to last day
        $this->assertLessThan($costLastDay, $costFirstDay);
    }
}

