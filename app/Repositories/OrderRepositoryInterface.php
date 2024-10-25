<?php

namespace App\Repositories;

interface OrderRepositoryInterface
{
   
    public function addOrder(array $orderData);

    public function getOrdersByBatch(string $hmoCode, int $providerId, string $searchDate );

}
