<?php

namespace App\Http\Controllers;

use App\Models\Insurer;
use Illuminate\Http\Request;
use App\Contracts\InsurerContract;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\InsurerRequest;

class InsurerController extends BaseController
{
    public $insurer;

    public function __construct(InsurerContract $insurer)
    {
        $this->insurer = $insurer;
    }

    public function list_all_insurer()
    {
        $insurers = $this->insurer->listInsurer();
        if($insurers)
        {
            return $this->responseJson($error = false, $responseCode = 200, $message = ['success' => 'Search was successful.'], $data = $insurers);
        }else{
            return $this->responseJson($error = true, $responseCode = 200, $message = ['success' => 'No record found.'], $data = null);
        }
    }

    public function save_insurer(InsurerRequest $request)
    {
        
        DB::beginTransaction();
        try {
            $params = $request->except('_token');
            $provider = $this->insurer->createInsurer($params);
            if ($provider) {
                DB::commit();
                return $this->responseJson($error = false, $responseCode = 200, $message = ['success' => 'Provider created successfully.'], $data = $provider);
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