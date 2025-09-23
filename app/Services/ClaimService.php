<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClaimService
{
    protected ClaimBatchingService $batchingService;

    public function __construct(ClaimBatchingService $batchingService)
    {
        $this->batchingService = $batchingService;
    }

    public function submit(array $data): Claim
    {
        return DB::transaction(function () use ($data) {
            $insurer = Insurer::with('configuration')
                ->where('code', $data['insurer_code'])
                ->firstOrFail();

            if (! $insurer->canProcessMoreClaims()) {
                throw new \Exception('Insurer has reached daily capacity limit');
            }

            $totalAmount = collect($data['items'])
                ->sum(fn ($item) => $item['unit_price'] * $item['quantity']);

            $claim = Claim::create([
                'provider_name' => $data['provider_name'],
                'insurer_code' => $data['insurer_code'],
                'encounter_date' => $data['encounter_date'],
                'submission_date' => Carbon::today(),
                'priority_level' => $data['priority_level'],
                'specialty' => $data['specialty'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
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

            $this->batchingService->processClaim($claim);

            return $claim;
        });
    }
}
