<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HasResponse
{
    protected function responseJson(mixed $data = [], string $message = '', int $responseCode = 200, bool $status = true): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $responseCode);
    }
}
