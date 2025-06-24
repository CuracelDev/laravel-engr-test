<?php

namespace App\Macros;

use Closure;

class ResponseMacro
{
    public function success(): Closure
    {
        return function (string $message, mixed $data = [], int $status = 200) {
            $response = (object) [
                'status' => 'success',
                'message' => $message,
                'data' => $data,
            ];

            return response()->json($response, $status);
        };
    }

    public function error(): Closure
    {
        return function (string $message, array $data = [], int $status = 400) {
            $response = (object) [
                'status' => 'error',
                'message' => $message,
                'data' => $data,
            ];

            return response()->json($response, $status);
        };
    }
}
