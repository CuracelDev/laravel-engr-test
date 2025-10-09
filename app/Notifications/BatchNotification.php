<?php

namespace App\Notifications;

use App\Models\Batch;
use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BatchNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Batch $batch,
        public Claim $claim
    ){}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Claim Submission Notification')
            ->greeting('Dear Insurer,')
            ->line('A new claim has just been submitted by a healthcare provider via the Claims Portal.')
            ->line('Please log in to your dashboard to review and process the claim details.')
            ->line('Summary of claim:')
            ->line('- Provider: ' . $this->claim->provider_name)
            ->line('- Encounter Date: ' . $this->claim->encounter_date)
            ->line('- Specialty: ' . $this->claim->specialty)
            ->line('- Total Amount: ₦' . number_format($this->claim->total_amount, 2))
            ->action('View Claim', url('' . $this->claim->id))
            ->line('Thank you for your continued partnership.');

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
