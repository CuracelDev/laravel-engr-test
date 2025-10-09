<?php

namespace App\Actions;

use App\Models\Claim;
use App\Models\Insurer;
use App\Models\Provider;
use App\Repositories\BatchingRepository;
use App\Repositories\ClaimRepository;
use Illuminate\Support\Facades\DB;

class ClaimAction
{
    public function __construct(
        public BatchingRepository $batchingRepository,
        public ClaimRepository $claimRepository
    ) {}

    

     /**
     * Execute business logic for storing a claim and assigning it to a batch.
     *
     * Returns the created Claim model with relations loaded.
     */
    public function execute(array $data): Claim
    {
        $insurer = Insurer::firstOrCreate(['code' => $data['insurer_code']]);
        $provider = Provider::firstOrCreate(['name' => $data['provider_name']]);

        return DB::transaction(function () use ($data, $insurer, $provider) {
            $claim = $this->claimRepository->create($data, $provider->id, $insurer->id);

            $total = 0;
            foreach ($data['items'] as $item) {
                $subtotal = $item['unit_price'] * $item['quantity'];
                $claim->items()->create([
                    'name' => $item['name'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ]);
                $total += $subtotal;
            }

            $claim->update(['total_amount' => $total]);


            $batch = $this->batchingRepository->assignClaimToBatch($claim);

            return $claim->load(['items', 'batch', 'provider', 'insurer']);
        });
    }
}
