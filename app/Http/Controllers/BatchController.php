<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Http\Requests\UpdateBatchRequest;
use App\Models\Claim;
use Inertia\Inertia;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $batches = Batch::where('insurer_id', auth()->user()->insurer->id)
            ->where('status', Batch::STATUS_PENDING)
            ->orderBy('date', 'desc')
            ->get();

        return Inertia::render('Dashboard', [
            'batches' => $batches,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Batch $batch)
    {
        $limit = request()->query('limit', 10);

        $claims = $batch->claims()
            ->with(['insurer', 'items'])
            ->orderBy('approximate_cost', 'desc')
            ->limit($limit)
            ->get();

        return Inertia::render('BatchDetails', [
            'batch' => $batch,
            'claims' => $claims,
            'defaultLimit' => $limit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBatchRequest $request, Batch $batch)
    {
        Claim::whereIn('id', $request->claims)->update(['status' => Claim::STATUS_PROCESSED]);

        return to_route('batch.show', $batch->id);
    }
}
