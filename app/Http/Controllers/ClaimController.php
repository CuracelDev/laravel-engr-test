<?php

namespace App\Http\Controllers;

use App\Actions\BatchClaim;
use App\Actions\ProcessBatch;
use App\Actions\SubmitClaim;
use App\Models\Insurer;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Claim Controller
 * 
 * Handles HTTP requests for claim submission.
 * Orchestrates Laravel Actions for business logic.
 */
class ClaimController extends Controller
{
    /**
     * Display the claim submission form
     * 
     * @return \Inertia\Response
     */
    public function create()
    {
        $insurers = Insurer::select('code', 'name')->get();
        return Inertia::render('SubmitClaim', compact('insurers'));
    }
}