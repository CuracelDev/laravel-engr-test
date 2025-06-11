<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use App\Models\Batch;
use App\Services\ClaimBatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\BatchSummaryNotification;
use Tests\TestCase;
use Carbon\Carbon;

class ClaimBatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function it_fails_validation_when_required_fields_are_missing()
    {
        $response = $this->postJson('/api/claims', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'insurer_code',
                'provider_name',
                'encounter_date',
                'specialty',
                'priority_level',
                'items',
            ]);
    }

    /** @test */
    public function it_creates_claim_and_batches_successfully()
    {
        $insurer = Insurer::factory()->create([
            'code' => 'ABC123',
        ]);

        $data = [
            'insurer_code' => $insurer->code,
            'provider_name' => 'HealthCare Inc.',
            'encounter_date' => '2025-06-10',
            'specialty' => 'cardiology',
            'priority_level' => 3,
            'items' => [
                ['name' => 'X-ray', 'quantity' => 2, 'unit_price' => 100],
                ['name' => 'Blood Test', 'quantity' => 1, 'unit_price' => 150],
            ]
        ];

        $response = $this->postJson('/api/claims', $data);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'processing_cost', 'batch_id']);

        $this->assertDatabaseHas('claims', [
            'provider_name' => 'HealthCare Inc.',
            'total_amount' => 350.00,
        ]);

        $this->assertDatabaseCount('claim_items', 2);

        $claim = Claim::first();
        $this->assertEquals(2, $claim->items()->count());

        $batch = Batch::first();
        $this->assertEquals($batch->id, $claim->batch_id);
        $this->assertEquals(1, $batch->claim_count);
        $this->assertEquals(350.00, $batch->total_amount);

        Mail::assertSent(BatchSummaryNotification::class, function ($mail) use ($batch) {
            return $mail->hasTo('shedrackogwuche5@gmail.com') && $mail->batch->is($batch);
        });
    }
}


