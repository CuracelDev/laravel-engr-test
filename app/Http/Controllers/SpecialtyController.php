<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class SpecialtyController extends Controller
{
    public function index(): JsonResponse
    {
        $specialties = [
            'cardiology' => 'Cardiology',
            'orthopedics' => 'Orthopedics',
            'neurology' => 'Neurology',
            'general' => 'General Medicine',
            'pediatrics' => 'Pediatrics',
        ];

        return response()->json([
            'success' => true,
            'data' => $specialties,
        ]);
    }
}
