<?php

namespace App\Listeners;

use App\Events\ClaimCreated;
use App\Models\Batch;
use App\Models\Claim;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateBatch implements ShouldQueue
{
    // protected $claim;

    /**
     * Create a new event instance.
     *
     */
    public function __construct()
    {
        // $this->claim = $claim;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ClaimCreated  $event
     * @return void
     */
    public function handle(ClaimCreated $event): void
    {
        $claim = $event->claim;
        info($claim);

        $batch = Batch::where('date', $claim->date)
            ->where('insurer_id', $claim->insurer_id)
            ->first();

        if (!$batch) {
            $batch = Batch::create([
                'date' => $claim->date,
                'insurer_id' => $claim->insurer_id,
                'name' => $claim->name . ' ' . date('F d Y', strtotime($claim->date)),
            ]);
        }

        $claim->approximate_cost = $this->generateApproxCost($claim);
        $claim->batch_id = $batch->id;
        $claim->save();
    }


    private function generateApproxCost(Claim $claim): float {
        $day = (int)(date('d', strtotime($claim->date)));

        $costPercentage = $day + config('constant.MIN_PROCCESSING_COST_PERCENTAGE');
        $costPercentage = $costPercentage > config('constant.MAX_PROCCESSING_COST_PERCENTAGE')
            ? config('constant.MAX_PROCCESSING_COST_PERCENTAGE') : $costPercentage;

        // processing
        $approxCost =  $claim->sub_total + ($claim->sub_total * ($costPercentage / 100));

        // speciality
        $approxCost -= $claim->insurer->speciality == $claim->speciality ? $claim->sub_total * 0.1 : 0;

        // priority level
        $approxCost += $claim->sub_total * ($claim->piority_level * 20) / 100;

        return $approxCost;
    }
}
