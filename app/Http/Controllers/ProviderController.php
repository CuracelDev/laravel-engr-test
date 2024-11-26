<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderController extends BaseController
{
    public $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function list_all_provider()
    {
        $providers = $this->provider->list_all();
        if($providers)
        {
            return $this->responseJson($error = false, $responseCode = 200, $message = ['success' => 'Provider created successfully.'], $data = $providers);
        }else{
            return $this->responseJson($error = true, $responseCode = 200, $message = ['success' => 'No record found.'], $data = null);
        }
    }

    public function save_provider(Request $request)
    {
        $validated = $request->validate([
            'provider_name' => 'required',
        ]);
        
        DB::beginTransaction();
        try {
            $params = $request->except('_token');
            $provider = $this->provider->save_provider($params);
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