<?php

namespace App\Console\Commands;

use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessYesterdayBatches extends Command
{
    protected $signature = 'batches:process-yesterday';
    protected $description = 'Mark yesterday\'s batches as processed and stamp processed_on';

    public function handle(): int
    {
        $yesterday = Carbon::yesterday()->toDateString();

        $query = Batch::whereDate('batch_date', $yesterday)
            ->where('status', 'queued');

        $count = $query->count();

        $query->update([
            'status'       => 'processed',
            'processed_on' => Carbon::today()->toDateString(),
        ]);

        $this->info("Processed {$count} batch(es) dated {$yesterday}.");
        return self::SUCCESS;
    }
}