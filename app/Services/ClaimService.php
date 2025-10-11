<?php 

namespace App\Services;

use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;
use App\Jobs\BatchClaimsJob;

class ClaimService
{
    public function createClaimWithItems(array $data)
    {
        return DB::transaction(function () use ($data) {
            $insurer = Insurer::where('code', $data['insurer_code'])->firstOrFail();

            $totalAmount = collect($data['items'])->sum(function ($item) {
                return $item['unit_price'] * $item['quantity'];
            });

            $claim = Claim::create([
                'insurer_id' => $insurer->id,
                'provider_name' => $data['provider_name'],
                'encounter_date' => $data['encounter_date'],
                'submission_date' => now()->toDateString(),
                'specialty' => $data['specialty'],
                'priority_level' => $data['priority_level'],
                'total_amount' => $totalAmount,
            ]);


            foreach ($data['items'] as $item) {
                ClaimItem::create([
                    'claim_id' => $claim->id,
                    'name' => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['unit_price'] * $item['quantity'],
                ]);
            }

            // Queue the batching job
            BatchClaimsJob::dispatch($insurer->id);

            return $claim->load('items');
        });
    }
}
