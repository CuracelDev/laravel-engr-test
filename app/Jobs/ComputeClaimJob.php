<?php

namespace App\Jobs;

use App\Actions\OptimizeBatchingAction;
use App\Models\Claim;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ComputeClaimJob implements ShouldQueue
{
    use Queueable;
    public $claim;

    /**
     * Create a new job instance.
     */
    public function __construct($claim)
    {
        $this->claim = $claim;
    }

    /**
     * Execute the job. check if the available jobs for that insurer is up to minimus batch
     */
    public function handle(): void
    {
        $claims = Claim::with(['insurer', 'specialtyEfficiency'])
        ->where('batched_at', null)
        ->where('processed_at', null)
        ->where('insurer_code',$this->claim->insurer_code)
        ->get();

        if (count($claims) >= $claims[0]?->insurer?->minimum_num_of_batch) {
            OptimizeBatchingAction::run($claims);
        }
    }

    public function onQueue()
    {
        return 'low-priority';
    }
}
