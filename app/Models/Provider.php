<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory, UUID;
    protected $guarded = ['id'];

    public function list_all($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc')
    {
        return self::orderBy($orderBy, $sortBy)->get($columns);
    }

    public function save_provider($payload)
    {
        return self::create($payload);
    }
}