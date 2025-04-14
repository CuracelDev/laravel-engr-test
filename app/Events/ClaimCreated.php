<?php

namespace App\Events;

use App\Models\Claim;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * The event fired when a new claim is created in the system.
 *
 * This event is triggered whenever a claim is successfully created,
 * allowing listeners to create batches or perform other actions
 *
 * @package App\Events
 */
class ClaimCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $claim;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Claim $claim The claim that was created
     */
    public function __construct(Claim $claim)
    {
        $this->claim = $claim;
    }

    /**
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('claim'),
        ];
    }
}
