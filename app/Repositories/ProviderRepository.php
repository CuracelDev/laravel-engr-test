<?php

namespace App\Repositories;

use App\Models\Provider;
use InvalidArgumentException;
use PhpParser\Node\Stmt\TryCatch;
use App\Contracts\ProviderContract;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class BaseRepository
 *
 * @package \App\Repositories
 */
class ProviderRepository extends BaseRepository implements ProviderContract
{

    public function __construct(Provider $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function listProviders(string $order = 'id', string $sort = 'desc', array $columns = ['*'])
    {
        return $this->all($columns, $order, $sort);
    }

    public function createProvider(array $params)
    {
        try {
            $provider = $model->createProvider($params);
            dd($params);
            return $provider;
        } catch (QueryException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}