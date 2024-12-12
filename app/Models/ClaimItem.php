<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
      'claim_id',
      'name',
      'unit_price',
      'quantity'
    ];
}
