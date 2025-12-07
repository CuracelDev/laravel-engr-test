<?php

namespace App\Console\Commands;

use App\Services\BatchingService;
use Illuminate\Console\Command;

class RunBatching extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:batch-claims';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the batching algorithm for all insurers';

    /**
     * Execute the console command.
     */
    public function handle(BatchingService $service)
    {
        $this->info("Running batching process...");
        $service->batchAll();
        $this->info("Batching completed.");
    }
}
