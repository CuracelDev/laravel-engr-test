<?php

namespace App\Http\Controllers;

use App\Events\ClaimCreated;
use App\Models\Claim;
use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\Batch;
use App\Models\Insurer;
use App\Notifications\NewClaim;
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
        $data = ClaimService::computClaimFigures($request->only([
            'insurer_id',
            'priority_level',
            'speciality',
            'name',
            'date',
            'items'
        ]));

        $claim = Claim::create($data);
        $claim->items()->createMany($data['items']);

        ClaimCreated::dispatch($claim);

        $claim->insurer->user->notify(new NewClaim($claim));

        return to_route('claim.index');
    }
}
