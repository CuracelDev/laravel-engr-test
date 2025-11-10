<?php

namespace App\Actions;

use App\Models\Insurer;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GetInsurerByCode
{
    use AsAction;

    public function handle(string $code): Insurer
    {
        return Insurer::where('code', strtoupper($code))->firstOrFail(['id', 'name', 'code']);
    }

    public function asController(string $code): JsonResponse
    {
        $insurer = $this->handle($code);

        return response()->json([
            'status' => __('success'),
            'message' => __('Insurer retrieved successfully.'),
            'data' => $insurer,
        ]);
    }
}
