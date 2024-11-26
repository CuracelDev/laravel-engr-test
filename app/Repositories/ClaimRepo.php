<?php

namespace App\Repositories;

use InvalidArgumentException;
use App\Contracts\BaseContracts;
use App\Contracts\ClaimContract;
use App\Models\Claim;
use App\Models\ClaimItem;
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
class ClaimRepo extends BaseRepository implements ClaimContract
{

    public function __construct(Claim $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function listClaim(string $order = 'id', string $sort = 'desc', array $columns = ['*'])
    {
        return $this->all();
    }

    public function findClaims(array $data)
    {
        return $this->findBy($data);
    }

    public function createClaim(array $params)
    {
        // Generate a unique claim code
        $code = generate_code(9, true);
        do {
            $code = generate_code(10, true);
        } while ($this->check_code($code));
        // Add code to form request
        $params += ['code' => $code, 'submission_date' => date('Y-m-d')];
        $save_claim =  $this->create($params);
        if ($save_claim) {
            // save all claim items
            $claim_id = $save_claim->id;
            if (ClaimItem::save_claim_items($params, $claim_id)) {
                return $save_claim;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }



    public function check_code($code)
    {
        $fetch = $this->findOneBy(['code' => $code]);
        return $fetch->code ?? "";
    }
}