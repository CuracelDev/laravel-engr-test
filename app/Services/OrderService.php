<?php

namespace App\Services;
use App\Repositories\OrderRepositoryInterface;
use App\Jobs\BatchedOrdersJob;

class OrderService {

    protected $orderRepositoryInterface;

    public function __construct(OrderRepositoryInterface $orderRepositoryInterface ){
        $this->orderRepositoryInterface = $orderRepositoryInterface;
    }

    public function addOrder(array $data){

        $result = $this->orderRepositoryInterface->addOrder($data);

        BatchedOrdersJob::dispatch($result['order'], $result['batch_name'], $result['hmo_email']);

    }


    public function getOrdersByBatch(string $hmoCode, int $providerId, string $searchDate){

        return $this->orderRepositoryInterface->getOrdersByBatch( $hmoCode,  $providerId,  $searchDate);

    }

}