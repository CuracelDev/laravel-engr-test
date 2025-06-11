<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Batch;

class BatchSummaryNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public Batch $batch;

    public function __construct(Batch $batch) { $this->batch = $batch; }


   public function build()
    {
        return $this->subject("Batch ready: {$this->batch->provider_name} on {$this->batch->batch_date}")
            ->view('emails.batch_summary');
    }
}
