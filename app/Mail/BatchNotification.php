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

    public Batch $batch;

    public string $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(Batch $batch, string $reason)
    {
        $this->batch = $batch;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Claims Batch Ready for Processing - {$this->batch->batch_identifier}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.batch-notification',
            with: [
                'batch' => $this->batch,
                'reason' => $this->reason,
                'reasonText' => $this->getReasonText(),
            ]
        );
    }

    protected function getReasonText(): string
    {
        return match ($this->reason) {
            'minimum_size_met' => 'Batch has reached the minimum required size',
            'maximum_size_reached' => 'Batch has reached maximum capacity',
            'end_of_day' => 'End of business day processing',
            'cost_efficient' => 'Batch processing is cost efficient',
            default => 'Batch is ready for processing'
        };
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
