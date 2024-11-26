<?php

namespace App\Contracts;

/**
 * Interface BaseContract
 * @package App\Contracts
 */
interface SpecialtyContract
{

    public function listSpecialty(string $order = 'id', string $sort = 'desc', array $columns = ['*']);

    public function findOneBy(array $data);

    public function createSpecialty(array $params);
}