<?php

namespace App\Repositories;

use App\Contracts\BaseContracts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
/**
 * Class BaseRepository
 *
 * @package \App\Repositories
 */
class BaseRepository implements BaseContracts
{
    /**
     *  Model
     */
    protected $model; 

    /**
     * BaseRepository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    //  array $attributes
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    } 

    public function all($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc')
    {
        return $this->model->orderBy($orderBy, $sortBy)->get($columns);
    }

    public function find(string $id)
    {
        return $this->model->find($id);
    }

    public function findOneOrFail(string $id)
    {
        return $this->model->findOrFail($id);
    }

    public function findBy(array $data)
    {
        return $this->model->where($data)->get();
    }

    public function findOneBy(array $data)
    {
    	return $this->model->where($data)->first();
    }
    
    public function findOneByOrFail(array $data)
    {
        return $this->model->where($data)->firstOrFail();
    }

}