<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification to notify Insurer of a new claim
 */
class NewClaim extends Notification implements ShouldQueue
{
    use Queueable;

    protected $claim;

    public function __construct(Claim $claim)
    {
        $this->claim = $claim;
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
     * Email template sent to Insurer
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Claim Received')
            ->greeting('Hello!')
            ->line('A new claim has been submitted with the following details:')
            ->line('Claim Name: ' . $this->claim->name)
            ->line('Priority Level: ' . $this->claim->priority_level)
            ->line('Specialty: ' . $this->claim->speciality)
            ->line('Date: ' . $this->claim->date)
            ->line('Sub Total: NGN ' . number_format($this->claim->sub_total, 2))
            ->action('View Claim Details', url('/claims/' . $this->claim->id))
            ->line('Thank you for using our claims system.');
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
