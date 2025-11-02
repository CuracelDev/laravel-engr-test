<?php

namespace App\Mail;

use App\Models\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BatchReadyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Batch $batch
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Claims Batch Ready - ' . $this->batch->batch_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.batch-ready',
            with: [
                'batch' => $this->batch,
                'insurer' => $this->batch->insurer,
                'claims' => $this->batch->claims,
            ],
        );
    }
}
