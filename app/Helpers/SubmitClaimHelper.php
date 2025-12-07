<?php

namespace App\Helpers;

use App\Models\Batch;
use App\Models\Claim;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubmitClaimHelper
{
    protected BatchClaimHelper $batchClaim;
    protected NotifyInsurerHelper $notifyInsurer;

    public function __construct(
        BatchClaimHelper $batchClaim,
        NotifyInsurerHelper $notifyInsurer
    ) {
        $this->batchClaim = $batchClaim;
        $this->notifyInsurer = $notifyInsurer;
    }

    public function execute(array $data): Claim
    {
        return DB::transaction(function () use ($data) {
            $insurerCode = strtoupper(trim($data['insurer_code']));
            $insurer = Insurer::whereRaw('UPPER(code) = ?', [$insurerCode])->firstOrFail();

            $totalAmount = collect($data['items'])->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $claim = Claim::create([
                'insurer_id' => $insurer->id,
                'provider_name' => $data['provider_name'],
                'encounter_date' => $data['encounter_date'],
                'submission_date' => Carbon::now()->format('Y-m-d'),
                'priority_level' => $data['priority_level'] ?? 3,
                'specialty' => $data['specialty'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            foreach ($data['items'] as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['unit_price'];
                $claim->items()->create([
                    'name' => $itemData['name'],
                    'unit_price' => $itemData['unit_price'],
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $subtotal,
                ]);
            }

            $batch = $this->batchClaim->execute($claim);

            $this->notifyInsurer->execute($batch);

            $claim->refresh();

            return $claim;
        });
    }
}
