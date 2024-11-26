<?php

namespace App\Contracts;

/**
 * Interface BaseContract
 * @package App\Contracts
 */
interface ProviderContract
{
  // List all provider
  public function listProviders(string $order = 'id', string $sort = 'desc', array $columns = ['*']);


  public function createProvider(array $params);

}