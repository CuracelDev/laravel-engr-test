<?php

namespace App\Notifications\Claims;

use App\Models\ClaimBatch;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBatchAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected ClaimBatch $batch)
    {
        $this->batch->loadMissing('provider', 'claims');
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
        $total_cost = $this->batch->claims->sum('total_amount');
        return (new MailMessage)
                    ->subject("New Claim Batch Submitted by {$this->batch?->provider?->name} on {$this->batch->batch_date}")
                    ->line("A new batch of healthcare claims has been submitted by {$this->batch?->provider?->name} and is now ready for processing.")
                    ->line("Provider: {$this->batch?->provider?->name}")
                    ->line("Processing Cost: {$this->batch->processing_cost}")
                    ->line("Total Cost: {$total_cost}")
                    ->line("Batch Date: {$this->batch->batch_date}")
                    ->line("Number of Claims: {$this->batch->claims->count()}")
                    ->action('View Batch', url('/'))
                    ->line('Thank you for using our application!');
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
