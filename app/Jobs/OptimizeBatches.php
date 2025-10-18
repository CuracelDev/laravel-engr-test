<?php

namespace App\Jobs;

use App\Jobs\NotifyInsurerOfBatch;
use App\Services\ClaimBatchingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OptimizeBatches implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $insurerId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $insurerId)
    {
        $this->insurerId = $insurerId;
    }

    /**
     * Execute the job.
     */
    public function handle(ClaimBatchingService $batchingService): void
    {
        Log::info("Starting batch optimization for insurer {$this->insurerId}");

        // Optimize claims for this specific insurer
        $stats = $batchingService->optimizeClaimsForInsurer($this->insurerId);

        Log::info("Batch optimization complete for insurer {$this->insurerId}", $stats);

        // Get batches that are ready but not yet notified
        $readyBatches = $batchingService->getReadyBatchesForInsurer($this->insurerId);

        // Dispatch notification jobs for ready batches
        foreach ($readyBatches as $batch) {
            NotifyInsurerOfBatch::dispatch($batch);
            Log::info("Dispatched notification for batch {$batch->identifier}");
        }

        if ($readyBatches->count() > 0) {
            Log::info("Dispatched {$readyBatches->count()} batch notification(s) for insurer {$this->insurerId}");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to optimize batches for insurer {$this->insurerId}: " . $exception->getMessage());
    }
}

