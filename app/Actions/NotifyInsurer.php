<?php

namespace App\Actions;

use App\Models\Insurer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Notify Insurer Action
 * 
 * Sends email notification to insurer when a batch is processed.
 * 
 * Email includes:
 * - Batch ID
 * - Number of claims processed
 * - Total processing cost
 */
class NotifyInsurer
{
    use AsAction;

    /**
     * Send email notification to insurer
     * 
     * @param Insurer $insurer The insurer to notify
     * @param Collection $claims Claims in the processed batch
     * @param string $batchId Batch identifier
     * @return void
     */
    public function handle(Insurer $insurer, Collection $claims, string $batchId): void
    {
        $totalCost = $claims->sum('processing_cost');
        $claimCount = $claims->count();

        Mail::raw(
            "Batch {$batchId} processed with {$claimCount} claims. Total processing cost: $" . number_format($totalCost, 2),
            fn($message) => $message->to($insurer->email)->subject("Batch Processed: {$batchId}")
        );
    }
}