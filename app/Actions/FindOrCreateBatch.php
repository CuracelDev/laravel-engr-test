<?php

namespace App\Actions;

use App\Enums\BatchStatus;
use App\Models\Batch;
use App\Models\Insurer;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class FindOrCreateBatch
{
    use AsAction;

    public function handle(Insurer $insurer, string $providerName, Carbon $batchDate): Batch
    {
        $batchIdentifier = Batch::generateIdentifier($providerName, $batchDate);

        return Batch::where('batch_identifier', $batchIdentifier)
            ->where('insurer_id', $insurer->id)
            ->whereIn('status', [BatchStatus::PENDING, BatchStatus::READY])
            ->firstOr(fn () => Batch::create([
                'insurer_id' => $insurer->id,
                'provider_name' => $providerName,
                'batch_date' => $batchDate,
                'processing_date' => $batchDate->copy()->addDay(),
                'status' => BatchStatus::PENDING,
                'batch_identifier' => $batchIdentifier,
            ]));

    }
}
