<?php

namespace Tests\Unit\Actions;

use App\Actions\CalculateEfficiencyScore;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CalculateEfficiencyScoreTest extends TestCase
{
    #[Test]
    public function it_calculates_score_with_low_cost_and_meets_minimum()
    {

        $score = (new CalculateEfficiencyScore())->handle(
            cost: 3500.0,
            meetsMinimum: true,
            date: now(),
            preferredDate: now()
        );

        // Cost: 3500 = 100 points (at 0.5 weight = 50)
        // Batch: true = 100 points (at 0.3 weight = 30
        // Date: 0 days = 100 points (at 0.2 weight = 20)
        // Total: 50 + 30 + 20 = 100
        $this->assertEquals(100.0, $score);
    }

    #[Test]
    public function it_calculates_score_with_medium_cost()
    {
        $score = (new CalculateEfficiencyScore())->handle(
            cost: 7500.0,
            meetsMinimum: true,
            date: now(),
            preferredDate: now()
        );

        // Cost: 7500 = 80 points (at 0.5 weight = 40)
        // Batch: true = 100 points (at 0.3 weight = 30)
        // Date: 0 days = 100 points (at 0.2 weight = 20)
        // Total: 40 + 30 + 20 = 90
        $this->assertEquals(90.0, $score);
    }

    #[Test]
    public function it_calculates_score_with_high_cost_and_not_meets_minimum()
    {
        $score = (new CalculateEfficiencyScore())->handle(
            cost: 22000.0,
            meetsMinimum: false,
            date: now(),
            preferredDate: now()
        );

        // Cost: 22000 = 20 points (at 0.5 weight = 10)
        // Batch: false = 50 points (at 0.3 weight = 15)
        // Date: 0 days = 100 points (at 0.2 weight = 20)
        // Total: 10 + 15 + 20 = 45
        $this->assertEquals(45.0, $score);
    }
}
