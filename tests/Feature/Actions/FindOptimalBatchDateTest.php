<?php

namespace Tests\Feature\Actions;

use App\Actions\FindOptimalBatchDate;
use App\Enums\DatePreference;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FindOptimalBatchDateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_finds_optimal_batch_date_based_on_constraints()
    {
        $insurer = Insurer::factory()->create([
            'daily_capacity_limit' => 100,
            'max_batch_size' => 50,
            'date_preference' => DatePreference::ENCOUNTER,
        ]);

        $claim = Claim::factory()->create([
            'insurer_id' => $insurer->id,
            'encounter_date' => now(),
            'submission_date' => now(),
        ]);

        $optimalDate = (new FindOptimalBatchDate())->handle($claim, $insurer);

        $this->assertInstanceOf(Carbon::class, $optimalDate);
    }
}
