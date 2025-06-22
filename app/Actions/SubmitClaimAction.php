<?php

namespace App\Actions;

use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Support\Facades\DB;

class SubmitClaimAction
{
    public function execute(array $data): Claim
    {
        $insurer = Insurer::where('code', $data['insurer_code'])->firstOrFail();

        $total = collect($data['items'])->sum(
            fn($item) => $item['unit_price'] * $item['quantity']
        );

        return DB::transaction(function () use ($data, $insurer, $total) {
            $claim = Claim::create([
                'provider_name' => $data['provider_name'],
                'insurer_id' => $insurer->id,
                'submission_date' => now(),
                'encounter_date' => $data['encounter_date'],
                'specialty' => $data['specialty'],
                'priority_level' => $data['priority_level'],
                'total_amount' => $total,
            ]);

            foreach ($data['items'] as $item) {
                $claim->items()->create([
                    'name' => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['unit_price'] * $item['quantity'],
                ]);
            }

            return $claim;
        });
    }
}
