<?php

namespace App\Notifications;

use App\Models\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BatchSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $batch;

    /**
     * Create a new notification instance.
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }

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
            ->subject('New Claims Batch Ready: ' . $this->batch->name)
            ->line('A new batch of claims has been prepared for processing.')
            ->line('Batch Name: ' . $this->batch->name)
            ->line('Total Claims: ' . $this->batch->total_claims)
            ->line('Total Amount: $' . number_format($this->batch->total_amount, 2))
            ->line('Please login to the portal to download the batch details.')
            ->action('View Batch', url('/nova/resources/batches/' . $this->batch->id)) // Assumes hypothetical admins panel or similar
            ->line('Thank you for your partnership!');
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
