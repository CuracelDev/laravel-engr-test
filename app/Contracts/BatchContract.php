<?php

namespace App\Contracts;


interface BatchContract
{
   
    public function listBatch(string $order = 'id', string $sort = 'desc', array $columns = ['*']);

 
    public function createBatch(array $params);

    
    public function findOneBy(array $data);
}