<?php

namespace App\Actions;

use App\Enums\PriorityLevel;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPriorities
{
    use AsAction;

    public function handle(): array
    {
        return PriorityLevel::toArray();
    }

    public function asController(): JsonResponse
    {
        $priorities = $this->handle();

        return response()->json([
            'status' => __('success'),
            'message' => __('Priorities retrieved successfully.'),
            'data' => $priorities,
        ]);
    }
}
