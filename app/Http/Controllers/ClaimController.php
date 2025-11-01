<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Mail\InsurerBatchNotification;
use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use App\Services\BatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class ClaimController extends Controller
{
    public function store(StoreClaimRequest $request, BatchingService $batcher): JsonResponse
    {
        $insurer = Insurer::where('code', $request->insurer_code)->firstOrFail();

        $payload = $request->validated();

        try {
            $claim = DB::transaction(function () use ($payload, $insurer, $batcher) {
                $computedItems = [];
                $total = 0.0;

                foreach ($payload['items'] as $row) {
                    $subtotal = round(((float)$row['unit_price']) * ((int)$row['quantity']), 2);
                    $total += $subtotal;

                    $computedItems[] = [
                        'name'       => $row['name'],
                        'unit_price' => $row['unit_price'],
                        'quantity'   => $row['quantity'],
                        'subtotal'   => $subtotal,
                    ];
                }

                // create claim
                $claim = Claim::create([
                    'insurer_id'      => $insurer->id,
                    'provider_name'   => $payload['provider_name'],
                    'encounter_date'  => $payload['encounter_date'],
                    'submission_date' => $payload['submission_date'],
                    'specialty'       => $payload['specialty'],
                    'priority_level'  => (int)$payload['priority_level'],
                    'items_count'     => count($computedItems),
                    'total_value'     => $total,
                    'status'          => 'pending',
                ]);

                // create items
                foreach ($computedItems as $it) {
                    ClaimItem::create(['claim_id' => $claim->id] + $it);
                }

                // batch & compute cost
                [$batch, $cost] = $batcher->assignToOptimalBatch($insurer, $claim);

                if ($insurer->notify_email) {
                    try {
                        Mail::to($insurer->notify_email)->queue(new InsurerBatchNotification($batch, $claim));
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }

                return $claim->fresh(['batch', 'items']);
            });
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Claim submitted and batched successfully',
            'claim'   => $claim,
        ], 201);
    }
}