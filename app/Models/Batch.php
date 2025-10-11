<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = ['insurer_id', 'provider_name', 'batch_date', 'total_value', 'status'];

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function insurer()
    {
        return $this->belongsTo(Insurer::class);
    }

}
