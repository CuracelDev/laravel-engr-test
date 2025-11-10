<?php

namespace App\Mail;

use App\Models\Batch;
use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimAddedToBatchNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Claim $claim, public readonly Batch $batch)
    {
        $this->batch->loadMissing(['claims', 'insurer']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Claim Added to Batch: {$this->batch->batch_identifier}");
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.claim-added-to-batch',
            with: [
                'claim' => $this->claim,
                'batch' => $this->batch,
                'insurer' => $this->batch->insurer,
                'claimCount' => $this->batch->claims()->count(),
                'totalAmount' => $this->batch->claims()->sum('total_amount'),
                'totalCost' => $this->batch->claims()->sum('processing_cost'),
            ]
        );
    }
}
