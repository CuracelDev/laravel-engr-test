<?php

namespace App\Actions;

use App\Enums\ClaimStatus;
use App\Http\Requests\SubmitClaimRequest;
use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class SubmitClaim
{
    use AsAction;

    public function handle(array $data): Claim
    {
        return DB::transaction(function () use ($data) {
            $insurer = Insurer::where('code', $data['insurer_code'])->firstOrFail();

            $items = collect($data['items']);

            $claim = Claim::create([
                'insurer_id' => $insurer->id,
                'provider_name' => $data['provider_name'],
                'total_amount' => $items->sum(fn ($item) => $item['unit_price'] * $item['quantity']),
                'encounter_date' => $data['encounter_date'],
                'submission_date' => now(),
                'priority_level' => $data['priority_level'],
                'specialty' => $data['specialty'],
                'status' => ClaimStatus::PENDING,
            ]);

            $itemsToInsert = $items->map(function ($item) use ($claim) {
                return [
                    'claim_id' => $claim->id,
                    'name' => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['unit_price'] * $item['quantity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            ClaimItem::insert($itemsToInsert->toArray());

            AssignClaimToBatch::run($claim);

            return $claim;
        });
    }

    public function asController(SubmitClaimRequest $request): JsonResponse
    {
        $claim = $this->handle($request->validated());

        return response()->json([
            'status' => __('success'),
            'message' => __('Claim submitted successfully.'),
            'data' => $claim,
        ]);
    }
}
