<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Jobs\BatchClaimsJob;
use App\Mail\NewBatchCreatedMail;
use App\Models\Batch;
use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use Illuminate\Support\Facades\Mail;
use Database\Seeders\InsurerSeeder;

class BatchClaimsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_claims_job_creates_batches_and_sends_email()
    {
        Mail::fake();
        $this->seed(InsurerSeeder::class);
        $insurer = Insurer::where('code', 'INS-A')->firstOrFail();

        $claims = Claim::factory()
            ->count(2)
            ->has(ClaimItem::factory()->count(3), 'items')
            ->create([
                'insurer_id' => $insurer->id,
                'provider_name' => 'Provider X',
                'encounter_date' => '2025-10-10',
                'batch_id' => null,
                'priority_level' => 3,
                'total_amount' => 100,
            ]);

        (new BatchClaimsJob($insurer->id))->handle();

        // Assert batch was created 
        $this->assertDatabaseHas('batches', [
            'provider_name' => 'Provider X',
            'insurer_id' => $insurer->id,
            'batch_date' => '2025-10-10',
            'status' => 'pending',
        ]);

        $batch = Batch::first();

        // Assert claims are updated with batch_id
        foreach ($claims as $claim) {
            $this->assertDatabaseHas('claims', [
                'id' => $claim->id,
                'batch_id' => $batch->id,
            ]);
        }

        // Assert batch attributes are correct
        $this->assertDatabaseHas('batches', $batch->only([
            'insurer_id',
            'provider_name',
            'batch_date',
            'status',
        ]));
        
        // Assert the email was queued to the insurer's email address
        Mail::assertQueued(NewBatchCreatedMail::class, function ($mail) use ($insurer) {
            return $mail->hasTo($insurer->email);
        });
    }
}
