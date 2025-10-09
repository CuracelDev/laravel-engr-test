<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Claim;
use App\Models\Insurer;
use App\Models\Provider;
use App\Models\Batch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BatchNotification;

class ClaimSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_claim_creates_and_batches_correctly()
    {
        Notification::fake();

        $provider = Provider::factory()->create();
        $insurer = Insurer::factory()->create([
            'batch_date_pref' => 'submission',
            'max_batch_size' => 5,
            'min_batch_size' => 2,
        ]);

        $payload = [
            'provider_name'   => $provider->name,
            'insurer_code'    => $insurer->code,
            'encounter_date'  => now()->toDateString(),
            'specialty'       => 'Cardiology',
            'priority_level'  => 1,
            'items' => [
                ['name' => 'ECG Test', 'unit_price' => 5000, 'quantity' => 1],
                ['name' => 'Consultation', 'unit_price' => 8000, 'quantity' => 1],
            ],
        ];


        $response = $this->postJson(route('api.claims.store'), $payload);


        $response->assertStatus(201);

        $claim = Claim::first();
        $batch = Batch::first();

        $this->assertNotNull($claim);
        $this->assertNotNull($batch);
        $this->assertEquals($claim->batch_id, $batch->id);
        $this->assertEquals($insurer->id, $batch->insurer_id);
        $this->assertEquals($provider->id, $batch->provider_id);

        // The batch should have correct totals
        $this->assertEquals(1, $batch->claim_count);
        $this->assertEquals($claim->total_amount, $batch->total_amount);

        // Notification should have been sent
     Notification::assertSentOnDemand(BatchNotification::class, function ($notification, $channels, $notifiable) use ($insurer, $claim) {
        return $notifiable->routes['mail'] === $insurer->email
            && $notification->batch
            && $notification->claim->id === $claim->id;
        });

    }
}
