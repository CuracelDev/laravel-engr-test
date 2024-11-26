<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Contracts\SpecialtyContract;

class SpecialtyController extends BaseController
{
    public $specialty;

    public function __construct(SpecialtyContract $specialty)
    {
        $this->specialty = $specialty;
    }

    public function list_all_specialty()
    {
        $specialties = $this->specialty->listSpecialty();
        if ($specialties) {
            return $this->responseJson($error = false, $responseCode = 200, $message = ['success' => 'Search was successful.'], $data = $specialties);
        } else {
            return $this->responseJson($error = true, $responseCode = 200, $message = ['success' => 'No record found.'], $data = null);
        }
    }

    public function save_specialty(Request $request)
    {
        $validated = $request->validate([
            'specialty_type' => 'required',
            'processing_cost' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $params = $request->except('_token');
            $specialty = $this->specialty->createSpecialty($params);
            if ($specialty) {
                DB::commit();
                return $this->responseJson($error = false, $responseCode = 200, $message = ['success' => 'Specialty created successfully.'], $data = $specialty);
            } else {
                DB::rollback();
                return $this->responseJson($error = true, $responseCode = 200, $message = ['error' => 'Error Encountered when trying to process request.'], $data = null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseJson($error = true, $responseCode = 200, $message = ['error' => 'Error Encountered when trying to process request.'], $data = null);
        }
    }
}