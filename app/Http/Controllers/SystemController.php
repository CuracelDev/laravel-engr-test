<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Insurer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SystemController extends Controller
{
    public function insurers()
    {
        return response()->json(
            Insurer::where('is_active', true)->get(['id','name','code'])
        );
    }

    public function batches(Request $request)
    {
        $date = $request->query('date');
        if ($date === 'yesterday' || !$date) {
            $date = Carbon::yesterday()->toDateString();
        }

        $batches = Batch::with('insurer')
            ->whereDate('batch_date', $date)
            ->orderBy('provider_name')
            ->get();

        return response()->json([
            'date'    => $date,
            'batches' => $batches,
        ]);
    }
}