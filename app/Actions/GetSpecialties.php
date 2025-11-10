<?php

namespace App\Actions;

use App\Enums\Specialty;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GetSpecialties
{
    use AsAction;

    public function handle(): array
    {
        return Specialty::toArray();
    }

    public function asController(): JsonResponse
    {
        $specialties = $this->handle();

        return response()->json([
            'status' => 'success',
            'message' => 'Specialties retrieved successfully.',
            'data' => $specialties,
        ]);
    }
}
