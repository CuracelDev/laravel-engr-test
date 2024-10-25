<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\BatchedOrdersMail;

class BatchedOrdersJob implements ShouldQueue
{
    use Queueable;

    public $orders;
    public $batchName;
    public $hmoEmail;

    /**
     * Create a new job instance.
     */
    public function __construct($orders, $batchName, $hmoEmail)
    {
        $this->orders = $orders;
        $this->batchName = $batchName;
        $this->hmoEmail = $hmoEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
       
        Mail::to($this->hmoEmail)
            ->send(new BatchedOrdersMail($this->orders, $this->batchName));
    }
}
