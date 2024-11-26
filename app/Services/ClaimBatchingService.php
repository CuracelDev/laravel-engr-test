<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Batch;
use App\Models\Claim;
use App\Repositories\BatchRepo;
use App\Repositories\ClaimRepo;
use App\Repositories\InsurerRepo;
use App\Repositories\SpecialtyRepo;


class ClaimBatchingService
{
    public $insurer;
    public $batch;
    public $claim;
    public $specialty;

    public function __construct(
        InsurerRepo $insurer,
        BatchRepo $batch,
        ClaimRepo $claim,
        SpecialtyRepo $specialty,
        
    ) {
        $this->insurer = $insurer;
        $this->batch = $batch;
        $this->claim = $claim;
        $this->specialty = $specialty;
    }

    public function process_batching()
    {
        $insurers = $this->insurer->listInsurer();
        foreach ($insurers as $key => $s_insurer) {
            $batches = [];
            $insurer_processing_cost = $s_insurer->processiing_cost;
            
            // fetch all insurer's pending claims 
            $insurers_claims = Claim::where([
                'insurer_id' => $s_insurer->id, 
                'status' => 'pending'])
                ->with('provider')
                ->get();
            $instatiate_batch = new Batch([
                'insurer_id' => $s_insurer->id,
                'batch_name' => "",
                'batch_date' => now(),
                'status' => 'pending',
                'total_claims' => 0,
                'monetary_value' => 0.00,
            ]);
            
            foreach ($insurers_claims as $claim) {
                $specialty = $this->specialty->findSpecialtyBy(['id' => $claim->specialty_id]);
                $insurer_processing_cost = $specialty->processing_cost;
                $priority_level = $claim->priority_level;
                $total_amt = $claim->total_amount;
                // calculate processing cost
                $claim->processing_cost = $this->calc_processing_cost($insurer_processing_cost, $priority_level, $total_amt);
                $claim->save();
                
                // Check if claim count is upto max claim size an insurer can process.
                if ($instatiate_batch->total_claims < $s_insurer->max_batch_size) {
                    $instatiate_batch->total_claims++;
                    $instatiate_batch->monetary_value += $claim->monetary_value;
                } else {
                    $batches[] = $instatiate_batch;
                    $batch_claim = new Batch([
                        'insurer_id' => $s_insurer->id,
                        'batch_name' => $claim->provider->provider_name."". Carbon::now()->format('M d, Y'),
                        'batch_date' => now(),
                        'status' => 'pending',
                        'total_claims' => 1,
                        'total_value' => $claim->monetary_value,
                    ]);
                }
            }

            $batches[] = $instatiate_batch;

            foreach ($batches as $batch) {
                $batch->save();
            }
        }
    }

    private function calc_processing_cost($default_cost, $priority_level, $total_amt)
    {
        /**
         * Time of the month factor : using the currrent day
         * $specialFactor: lEts assume its 1
         */
        $timeOfMonthFactor = (now()->day / 30) * 0.3 + 0.2;
        $specialtyFactor = 1; // Implement specialty-specific cost factor
        $priorityFactor = $priority_level * 0.1;
        $monetaryValueFactor = $total_amt * 0.01;

        return $default_cost * (1 + $timeOfMonthFactor + $specialtyFactor + $priorityFactor + $monetaryValueFactor);
    }

    public function get_all_batched()
    {
        return $this->batch->listBatch();
    }
}