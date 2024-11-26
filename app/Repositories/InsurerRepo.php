<?php

namespace App\Repositories;

use InvalidArgumentException;
use App\Contracts\BaseContracts;
use App\Contracts\InsurerContract;
use App\Models\Insurer;
use PhpParser\Node\Stmt\TryCatch;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class BaseRepository
 *
 * @package \App\Repositories
 */
class InsurerRepo extends BaseRepository implements InsurerContract
{

    public function __construct(Insurer $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function listInsurer(string $order = 'id', string $sort = 'desc', array $columns = ['*'])
    {
        return $this->all();
    }

    public function findInsurerBy(array $data)
    {
        return $this->findOneBy($data);
    }

    public function createInsurer(array $params)
    {
        try {
            $code = generate_code(6, true);
            $params += ['code' => $code];
            return $this->create($params);
        } catch (QueryException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}