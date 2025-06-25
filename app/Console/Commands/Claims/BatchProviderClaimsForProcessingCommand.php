<?php

namespace App\Console\Commands\Claims;

use App\Models\Insurer;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ProcessProviderClaimsForInsurerJob;

class BatchProviderClaimsForProcessingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:batch-provider-claims-for-processing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds provider claims to the batch for processing by insurers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $processing_batch = Bus::batch([])
        ->then(function (Batch $batch) {
        })->name('Process providers claims into batches')
        ->dispatch();


        Insurer::withWhereHas('claims', fn ($insurer) => $insurer->whereNull('processed_at'))
            ->chunkById(10, function ($insurers) use ($processing_batch) {
                $insurers->each(function (Insurer $insurer) use ($processing_batch) {
                    $insurer->claims->groupBy('provider_id')
                                    ->each(fn ($claims, $provider_id) => $processing_batch->add(new ProcessProviderClaimsForInsurerJob($insurer, $claims, $provider_id)));
                });
            });
        
        $this->info('Claims batched for processing');
    }
}
