<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class ProcessOrderJob implements ShouldQueue
{
    use Queueable;

    public $orders;

    /**
     * Create a new job instance.
     */
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->orders->each(function($order){
            $order->update(['status', 'processed']);
        });
    }
}
