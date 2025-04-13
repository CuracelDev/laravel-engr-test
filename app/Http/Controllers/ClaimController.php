<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\Insurer;
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
        info($request->all());

        return to_route('claim.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Claim $claim)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Claim $claim)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClaimRequest $request, Claim $claim)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Claim $claim)
    {
        //
    }
}
