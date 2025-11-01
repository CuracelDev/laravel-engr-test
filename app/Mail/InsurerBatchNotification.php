<?php

namespace App\Mail;

use App\Models\Batch;
use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InsurerBatchNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Batch $batch, public Claim $claim) {}

    public function build()
    {
        return $this->subject("New Claim in Batch {$this->batch->batch_code}")
            ->view('emails.insurer_batch_notification', [
                'batch' => $this->batch,
                'claim' => $this->claim,
            ]);
    }
}