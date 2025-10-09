<?php

namespace App\Models;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = ['batch_identifier','provider_id','insurer_id','batch_date','claim_count','total_amount'];

    public function claims() { return $this->hasMany(Claim::class); }
}
