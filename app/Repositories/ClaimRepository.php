<?php

namespace App\Repositories;

use App\Models\Claim;

class ClaimRepository
{
    public function create(array $data, $provider_id, $insurer_id)
    {
        return Claim::create([
                'provider_id' => $provider_id,
                'insurer_id' => $insurer_id,
                'encounter_date' => $data['encounter_date'],
                'submission_date' => now()->toDateString(),
                'priority_level' => $data['priority_level'],
                'specialty' => $data['specialty'],
                'total_amount' => 0,
            ]);
    }
}