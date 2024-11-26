<?php

namespace App\Contracts;

interface BaseContracts
{
    // Create a model instance
     public function create(array $attributes);

    
     // Return all model rows
    public function all($columns = array('*'), string $orderBy = 'id', string $sortBy = 'desc');

    
    // Find one by ID
    public function find(string $id);

    
    // Find one by ID or throw exception
    public function findOneOrFail(string $id); 

    
    // Find based on a different column
    public function findBy(array $data);

    
    // Find one based on a different column
    public function findOneBy(array $data);

    // Find one based on a different column or through exception
    public function findOneByOrFail(array $data);
}