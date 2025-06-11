<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Batch;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BatchSummaryNotification;
use App\Http\Requests\StoreClaimRequest;

class ClaimBatchingService
{
    public function createAndBatch(StoreClaimRequest $request): Claim
    {
        $insurer = Insurer::where('code', $request->insurer_code)->firstOrFail();

        $claim = Claim::create([
            'insurer_id' => $insurer->id,
            'provider_name' => $request->provider_name,
            'encounter_date' => $request->encounter_date,
            'specialty' => $request->specialty,
            'priority_level' => $request->priority_level,
            'processing_cost' => 0,
            'batch_id' => 0,
            'total_amount' => collect($request->items)->sum(function ($item) {
              return $item['unit_price'] * $item['quantity'];
          }),
        ]);

        foreach ($request->items as $i) {
            ClaimItem::create([
                'claim_id' => $claim->id,
                'name' => $i['name'],
                'quantity' => $i['quantity'],
                'unit_price' => $i['unit_price'],
            ]);
        }

        $processingCost = $this->computeCost($claim, $insurer);
        $claim->processing_cost = $processingCost;

        $batch = $this->assignToBatch($claim, $insurer);
        $claim->batch()->associate($batch);
        $claim->save(); 
        $batch->total_amount += $claim->total_amount;
        $batch->total_processing_cost += $claim->processing_cost;
        $batch->claim_count += 1;
        $batch->save();

        Mail::to('shedrackogwuche5@gmail.com')
            ->send(new BatchSummaryNotification($batch));

        return $claim;
    }

    public function computeCost(Claim $claim, Insurer $insurer): float
    {
        $d = Carbon::parse($claim->submission_date)->day;
        $date_penalty = 0.20 + (($d - 1) * (0.30 / 29));

        $specialty_factor = $insurer->specialty_cost_factors[$claim->specialty] ?? 1.0;
        $priority_multiplier = 1 + ($claim->priority_level * 0.05);
        $value_multiplier = min($claim->total_amount / 1000, 1) * 0.10;

        $base = $claim->total_amount;

        return round($base * ($date_penalty + $specialty_factor + $priority_multiplier + $value_multiplier), 2);
    }

    public function assignToBatch(Claim $claim, Insurer $insurer): Batch
    {
        $dateField = $insurer->date_preference; 
        $batchDate = Carbon::parse($claim->{$dateField . '_date'})->subDay();

        $batch = Batch::firstOrCreate(
            [
                'provider_name' => $claim->provider_name,
                'insurer_id' => $insurer->id,
                'batch_date' => $batchDate->toDateString(),
            ],
            [
                'total_amount' => 0,
                'total_processing_cost' => 0,
                'claim_count' => 0,
            ]
        );

        $claim->batch_id = $batch->id;
        $claim->save();

        //  Optional: handle capacity or size limits here
        // $count = $batch->claims()->count();
        // if ($count >= $insurer->max_batch_size) {
        //     throw new \Exception("Batch size limit exceeded for {$insurer->name}");
        // }

        return $batch;
    }
}
