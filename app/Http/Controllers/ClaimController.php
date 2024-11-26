<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\ClaimContract;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ClaimRequest;
use App\Repositories\InsurerRepo;

class ClaimController extends BaseController
{
    public $claim;

    public function __construct(ClaimContract $claim)
    {
        $this->claim = $claim;
    }
    
    public function process_claim(Request $request, InsurerRepo $insurer)
    {
        // dd($request->all());
        // DB::beginTransaction();
        // try {
        $get_insurer = $insurer->findInsurerBy(['code' => $request->insurer_code]);
        if(isset($get_insurer))
        {
            $params = $request->except('_token');
            $params += ['insurer_id' => $get_insurer->id];
            $save_claim = $this->claim->createClaim($params);
            if ($save_claim) {
                // DB::commit();
                return $this->responseJson($error = false, $responseCode = 200, $message = ['success' => 'Provider created successfully.'], $data = $save_claim);
            } else {
                // DB::rollback();
                return $this->responseJson($error = true, $responseCode = 200, $message = ['error' => 'Error Encountered when trying to process request.'], $data = null);
            }
        }else{
            return $this->responseJson($error = true, $responseCode = 200, $message = ['error' => 'Insurer Code not correct.'], $data = null);
        }
            
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return $this->responseJson($error = true, $responseCode = 200, $message = ['error' => 'Error Encountered when trying to process request.'], $data = null);
        // }
    }
}