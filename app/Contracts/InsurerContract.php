<?php

namespace App\Contracts;

/**
 * Interface BaseContract
 * @package App\Contracts
 */
interface InsurerContract
{

    public function listInsurer(string $order = 'id', string $sort = 'desc', array $columns = ['*']);


    public function createInsurer(array $params);

    
    public function findOneBy(array $data);
}