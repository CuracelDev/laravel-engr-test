<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ClaimBatchingService;

class BatchClaimController extends BaseController
{
    public $batches;

    public function __construct(ClaimBatchingService $batches)
    {
        $this->batches = $batches;
    }

    public function get_all_batched()
    {
        return $this->batches->get_all_batched();
    }

    public function process_batches()
    {
        // DB::beginTransaction();
        $process_claims =  $this->batches->process_batching();;
        // if ($process_claims) {
        //     DB::commit();
        //     return $this->responseJson($error = false, $responseCode = 200, $message = ['success' => 'Claim batching was successful.'], $data = null);
        // } else {
        //     DB::rollback();
        //     return $this->responseJson($error = true, $responseCode = 200, $message = ['error' => 'Error Encountered when trying to process request.'], $data = null);
        // }
    }
}