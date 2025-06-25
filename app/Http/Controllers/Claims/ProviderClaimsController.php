<?php

namespace App\Http\Controllers\Claims;

use Inertia\Inertia;
use App\Models\Insurer;
use Illuminate\Http\Request;
use App\Actions\Claims\Create;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\ProviderClaims\CreateRequest;

class ProviderClaimsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $claims = auth()->user()->claims()
                ->with('insurer:id,name,code')
                ->withCount('items')
                ->when(request('search'), fn ($claims, $search) => $claims->where('specialty', 'like', "%{$search}%") )
                ->orderBy('created_at', 'desc')
                ->get();
        return Inertia::render('Claims/ListClaims', compact('claims'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Claims/SubmitClaim', [
            'insurers' => Insurer::select('id', 'code', 'name')->get(),
            'specialties' => config('constants.specialists'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        try {
            (new Create)($request->validated());
            return to_route('provider-claims.index');
        } catch (\Throwable $th) {
            report($th);
            return redirect()->back()->with('error', 'Claims could not be created, please try again!');
        }
    }
}
