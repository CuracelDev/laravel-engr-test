<?php

namespace Feature\Actions;

use App\Actions\FindOrCreateBatch;
use App\Enums\BatchStatus;
use App\Models\Batch;
use App\Models\Insurer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FindOrCreateBatchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_finds_existing_batch()
    {
        $insurer = Insurer::factory()->create();
        $providerName = 'Test Provider';
        $batchDate = now();
        $bathIdentifier = Batch::generateIdentifier($providerName, $batchDate);

        $existingBatch = Batch::factory()->create([
            'insurer_id' => $insurer->id,
            'provider_name' => $providerName,
            'batch_date' => $batchDate,
            'batch_identifier' => $bathIdentifier,
            'status' => BatchStatus::PENDING,
        ]);

        $batch = (new FindOrCreateBatch())->handle($insurer, 'Test Provider', $batchDate);

        $this->assertEquals($existingBatch->id, $batch->id);
    }

    #[Test]
    public function it_creates_new_batch_when_none_exists()
    {
        $insurer = Insurer::factory()->create();

        (new FindOrCreateBatch())->handle($insurer, 'New Provider', now());

        $this->assertDatabaseHas(Batch::class, [
            'provider_name' => 'New Provider',
            'insurer_id' => $insurer->id,
        ]);
    }
}
