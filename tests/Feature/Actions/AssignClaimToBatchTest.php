<?php

namespace Tests\Feature\Actions;

use App\Actions\AssignClaimToBatch;
use App\Enums\ClaimStatus;
use App\Mail\ClaimAddedToBatchNotification;
use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssignClaimToBatchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_assigns_claim_to_batch_and_sends_claim_added_notification()
    {
        Mail::fake();

        $insurer = Insurer::factory()->create(['min_batch_size' => 1]);
        $claim = Claim::factory()->create(['insurer_id' => $insurer->id]);

        (new AssignClaimToBatch())->handle($claim);

        $claim->refresh();

        $this->assertEquals(ClaimStatus::BATCHED, $claim->status);
        $this->assertNotNull($claim->batch_id);

        Mail::assertQueued(ClaimAddedToBatchNotification::class, fn ($mail) => $mail->hasTo($insurer->email));
    }

    #[Test]
    public function it_assigns_claim_to_batch_without_marking_ready_when_min_batch_size_not_reached()
    {
        Mail::fake();

        $insurer = Insurer::factory()->create(['min_batch_size' => 5]);
        $claim = Claim::factory()->create(['insurer_id' => $insurer->id]);

        (new AssignClaimToBatch())->handle($claim);

        $claim->refresh();

        $this->assertEquals(ClaimStatus::BATCHED, $claim->status);
        $this->assertNotNull($claim->batch_id);

        Mail::assertQueued(ClaimAddedToBatchNotification::class, fn ($mail) => $mail->hasTo($insurer->email));
    }
}
