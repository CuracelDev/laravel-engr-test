<?php

namespace Tests\Unit;

use App\Models\Claim;
use App\Models\Insurer;
use App\Services\BatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_batches_claims_respecting_min_batch_size()
    {
        Notification::fake();

        $insurer = Insurer::factory()->create([
            'min_batch_size' => 3,
            'max_batch_size' => 10,
            'daily_processing_capacity' => 10,
            'date_preference' => 'encounter',
        ]);

        // Create 2 claims (should not batch)
        Claim::factory()->count(2)->create([
            'insurer_id' => $insurer->id,
            'provider_name' => 'Dr. Test',
            'encounter_date' => '2024-01-01',
            'status' => 'pending',
        ]);

        $service = new BatchingService();
        $service->processInsurer($insurer);

        $this->assertDatabaseCount('batches', 0);
        $this->assertDatabaseHas('claims', ['status' => 'pending']);

        // Add 1 more (Total 3 - should batch)
        Claim::factory()->create([
            'insurer_id' => $insurer->id,
            'provider_name' => 'Dr. Test',
            'encounter_date' => '2024-01-01',
            'status' => 'pending',
        ]);

        $service->processInsurer($insurer);

        $this->assertDatabaseCount('batches', 1);
        $this->assertDatabaseHas('claims', ['status' => 'batched']);
    }

    public function test_it_respects_daily_capacity()
    {
        Notification::fake();

        $insurer = Insurer::factory()->create([
            'min_batch_size' => 1,
            'max_batch_size' => 10,
            'daily_processing_capacity' => 2, // Only 2 claims per day
            'date_preference' => 'encounter',
        ]);

        // Create 5 claims
        Claim::factory()->count(5)->create([
            'insurer_id' => $insurer->id,
            'provider_name' => 'Dr. Test',
            'encounter_date' => '2024-01-01',
            'status' => 'pending',
        ]);

        $service = new BatchingService();
        $service->processInsurer($insurer);

        // Should have batched 2 claims, 3 remain pending
        $this->assertEquals(3, Claim::where('status', 'pending')->count());
        $this->assertEquals(2, Claim::where('status', 'batched')->count());
    }

    public function test_it_prioritizes_higher_value_batches()
    {
        Notification::fake();

        $insurer = Insurer::factory()->create([
            'min_batch_size' => 1,
            'max_batch_size' => 10,
            'daily_processing_capacity' => 1, // Only 1 claim allowed
            'date_preference' => 'encounter',
        ]);

        // Claim 1: High Value
        $c1 = Claim::factory()->create([
            'insurer_id' => $insurer->id,
            'provider_name' => 'High Value',
            'encounter_date' => '2024-01-01',
            'amount' => 1000,
            'priority' => 5,
            'status' => 'pending',
            'specialty' => 'general',
        ]);

        // Claim 2: Low Value
        $c2 = Claim::factory()->create([
            'insurer_id' => $insurer->id,
            'provider_name' => 'Low Value',
            'encounter_date' => '2024-01-01',
            'amount' => 10,
            'priority' => 1,
            'status' => 'pending',
            'specialty' => 'general',
        ]);

        $service = new BatchingService();
        $service->processInsurer($insurer);

        // High Value should be batched
        $this->assertDatabaseHas('claims', ['id' => $c1->id, 'status' => 'batched']);
        $this->assertDatabaseHas('claims', ['id' => $c2->id, 'status' => 'pending']);
    }
}
