<?php

namespace App\Contracts;

/**
 * Interface BaseContract
 * @package App\Contracts
 */
interface ClaimContract
{
    // list all model items
    public function listClaim(string $order = 'id', string $sort = 'desc', array $columns = ['*']);

    public function createClaim(array $params);

    public function findOneBy(array $data);

    public function findBy(array $data);
}