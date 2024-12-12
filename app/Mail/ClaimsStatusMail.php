<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimsStatusMail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $emailData;
    /**
     * Create a new message instance.
     */
    public function __construct($subject, $emailData)
    {
        $this->subject = $subject;
        $this->emailData = $emailData;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->to($this->emailData['insurerEmail'])
            ->view('emails.claim_status_update')
            ->with([
                'insurerName' =>  $this->emailData['insurerName'],
                'movedClaims' =>  $this->emailData['movedClaims'],
                'processedClaims' => $this->emailData['processedClaims'],
                'totalMoved' => $this->emailData['totalMoved'],
                'totalProcessed' => $this->emailData['totalProcessed'],
            ]);
    }
}
