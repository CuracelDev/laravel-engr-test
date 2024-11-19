<?php

namespace App\Jobs;

use App\Actions\OptimizeBatchingAction;
use App\Models\Claim;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduleBatchJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $claims = Claim::with(['insurer', 'specialtyEfficiency'])
        ->where('batched_at', null)
        ->where('processed_at', null)
        ->distinct('insurer_code')
        ->get();
        foreach($claims as $key => $claim){
            OptimizeBatchingAction::run($claim);
        }
    }
}
