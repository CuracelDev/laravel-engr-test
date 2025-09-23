<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\JsonResponse;

class BatchController extends Controller
{
    public function index(): JsonResponse
    {
        $batches = Batch::with(['claims', 'insurer'])->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }

    public function show(Batch $batch): JsonResponse
    {
        $batch->load(['claims.items', 'insurer']);

        return response()->json([
            'success' => true,
            'data' => $batch,
        ]);
    }
}
