<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\Batch;
use App\Models\Insurer;
use App\Services\ClaimService;
use Inertia\Inertia;

class ClaimController extends Controller
{
    /**
     * Display a listing of the claims.
     */
    public function index()
    {
        return Inertia::render('Claims');
    }

    /**
     * Show the form for creating a new claim.
     */
    public function create()
    {
        $insurers = Insurer::all();

        return Inertia::render('SubmitClaim', [
            'insurers' => $insurers,
            'priorities' => Claim::PRIORITES,
            'specialties' => Claim::SPECIALTIES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClaimRequest $request)
    {
        $data = $request->only([
            'insurer_id',
            'priority_level',
            'speciality',
            'name',
            'date',
        ]);

        // TODO Comput claim figure to cover sub_total and total
        $data['sub_total'] = ClaimService::calculateSubTotal($request->items);

        $claim = Claim::create($data);
        $claim->items()->createMany(array_map(function ($item) {
            return [
                ...$item,
                'total_price' => $item['quantity'] * $item['unit_price'],
            ];
        }, $request->items));

        $this->createBatchClaim($claim);

        return to_route('claim.index');
    }

    private function createBatchClaim(Claim $claim)
    {
        info($claim);

        $batch = Batch::where('date', $claim->date)
            ->where('insurer_id', $claim->insurer_id)
            ->first();

        if (!$batch) {
            $batch = Batch::create([
                'date' => $claim->date,
                'insurer_id' => $claim->insurer_id,
                'name' => $claim->name . ' ' . date('F d Y', strtotime($claim->date)),
            ]);
        }

        $claim->approximate_cost = $this->generateApproxCost($claim);
        $claim->batch_id = $batch->id;
        $claim->save();

        info($batch);
    }


    private function generateApproxCost(Claim $claim): float {
        $day = (int)(date('d', strtotime($claim->date)));

        $costPercentage = $day + config('constant.MIN_PROCCESSING_COST_PERCENTAGE');
        $costPercentage = $costPercentage > config('constant.MAX_PROCCESSING_COST_PERCENTAGE')
            ? config('constant.MAX_PROCCESSING_COST_PERCENTAGE') : $costPercentage;

        // processing
        $approxCost =  $claim->sub_total + ($claim->sub_total * ($costPercentage / 100));

        // speciality
        $approxCost -= $claim->insurer->speciality == $claim->speciality ? $claim->sub_total * 0.1 : 0;

        // priority level
        $approxCost += $claim->sub_total * ($claim->piority_level * 20) / 100;

        return $approxCost;
    }
}
