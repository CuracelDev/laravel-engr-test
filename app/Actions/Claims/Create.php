<?php

namespace App\Actions\Claims;

use App\Models\Claim;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Create
{
    public function __invoke(array $data): Claim
    {
        return DB::transaction(function () use ($data) {
            $claim = auth()->user()
                        ->claims()
                        ->create(
                            Arr::except($data, ['items'])
                        );
            $claim->items()->createMany($data['items']);
            Cache::forget(auth()->id() . '_claims_summary');
            return $claim;
        });
    }
}