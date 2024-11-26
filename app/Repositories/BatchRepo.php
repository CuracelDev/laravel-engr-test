<?php

namespace App\Repositories;

use InvalidArgumentException;
use App\Contracts\BaseContracts;
use App\Contracts\BatchContract;
use App\Models\Batch;
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
class BatchRepo extends BaseRepository implements BatchContract
{

    public function __construct(Batch $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function listBatch(string $order = 'id', string $sort = 'desc', array $columns = ['*'])
    {
        return $this->all();
    }

    public function createBatch(array $params)
    {
            $code = generate_code(6, true);
            $params += ['code' => $code];
            return $this->create($params);
    }
}