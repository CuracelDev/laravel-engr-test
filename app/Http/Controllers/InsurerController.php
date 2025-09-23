<?php

namespace App\Http\Controllers;

use App\Models\Insurer;
use Illuminate\Http\JsonResponse;

class InsurerController extends Controller
{
    public function index(): JsonResponse
    {
        $insurers = Insurer::with('configuration')->get();

        return response()->json([
            'success' => true,
            'data' => $insurers,
        ]);
    }
}
