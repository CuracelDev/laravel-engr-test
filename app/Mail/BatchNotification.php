<?php

namespace App\Mail;

use App\Models\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BatchNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Batch $batch)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Claims Batch Ready for Processing - ' . $this->batch->provider_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.batch-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
