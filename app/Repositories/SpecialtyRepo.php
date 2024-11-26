<?php

namespace App\Repositories;

use App\Contracts\SpecialtyContract;
use App\Models\Specialty;
use InvalidArgumentException;
use App\Repositories\BaseRepository;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class BaseRepository
 *
 * @package \App\Repositories
 */
class SpecialtyRepo extends BaseRepository implements SpecialtyContract
{

    public function __construct(Specialty $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function listSpecialty(string $order = 'id', string $sort = 'desc', array $columns = ['*'])
    {
        return $this->all();
    }

    public function findSpecialtyBy(array $data)
    {
        return $this->findOneBy($data);
    }

    public function createSpecialty(array $params)
    {
        try {
            return $this->create($params);
        } catch (QueryException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}