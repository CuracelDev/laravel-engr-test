<?php

namespace App\Jobs;

use App\Enums\BatchStatus;
use App\Mail\BatchReadyNotification;
use App\Models\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyInsurerOfBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Batch $batch;

    /**
     * Create a new job instance.
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load relationships
        $this->batch->load(['insurer', 'claims']);

        // Send email notification (using log driver in development)
        // In production, this would send to the insurer's email
        $emailAddress = config('mail.from.address'); // In real scenario: $this->batch->insurer->email
        
        Mail::to($emailAddress)->send(new BatchReadyNotification($this->batch));

        // Update batch status
        $this->batch->status = BatchStatus::NOTIFIED;
        $this->batch->notified_at = now();
        $this->batch->save();

        Log::info("Batch notification sent for batch {$this->batch->identifier}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to send batch notification for batch {$this->batch->identifier}: " . $exception->getMessage());
    }
}

