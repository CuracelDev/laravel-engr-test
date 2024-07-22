<?php

namespace App\Listeners;

use App\Events\CreateOrder;
use App\Notifications\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateOrderListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CreateOrder $event): void
    {
        // $event->hmo->notify(new OrderCreated($event->order));
    }
}
