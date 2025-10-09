<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        // Get the list of specialties from the database
        $specialties = Specialty::all();

        return response()->json($specialties);
    }
}
