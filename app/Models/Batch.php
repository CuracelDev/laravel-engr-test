<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'insurer_id',
        'batch_date',
        'name',
        'total_claims',
        'total_amount',
    ];

    public function insurer()
    {
        return $this->belongsTo(Insurer::class);
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
}
