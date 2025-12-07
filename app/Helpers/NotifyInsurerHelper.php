<?php

namespace App\Helpers;

use App\Mail\BatchNotification;
use App\Models\Batch;
use Illuminate\Support\Facades\Mail;

class NotifyInsurerHelper
{
    public function execute(Batch $batch): void
    {
        if ($batch->claim_count >= $batch->insurer->min_batch_size) {
            Mail::to($batch->insurer->email)->send(new BatchNotification($batch));

            $batch->update([
                'status' => 'notified',
                'notified_at' => now(),
            ]);
        }
    }
}
