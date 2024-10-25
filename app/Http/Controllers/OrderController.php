<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\GetOrdersByBatchRequest;
use App\Services\OrderService;
use Inertia\Inertia;
use App\Models\Hmo;
use App\Models\Provider;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    protected $orderService;
   
    public function __construct(OrderService $orderService){
        $this->orderService = $orderService;
    }

    public function index(){

        $hmos = Hmo::all();
        $providers = Provider::all();
       
        return Inertia::render('SubmitOrder', [
            'hmos' => $hmos,
            'providers' => $providers
        ]);
       
    }



    public function submitOrder(OrderRequest $request){

        $validatedData = $request->validated();

        $this->orderService->addOrder($validatedData);

        return redirect()->back()->with('success', 'Order submitted successfully!');
    }

    public function getOrdersByBatch(GetOrdersByBatchRequest $request){

        $validatedData = $request->validated();

        $hmoCode = $validatedData['hmo_code'];
        $providerId = $validatedData['provider_id'];
        $searchDate = $validatedData['search_date'];

        $orders = $this->orderService->getOrdersByBatch( $hmoCode,  $providerId,  $searchDate);

    }
}
