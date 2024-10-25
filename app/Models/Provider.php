<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Batch;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = ["name"];


    public function batches()
    {
        return $this->hasMany(Batch::class);
    }
}
