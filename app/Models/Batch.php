<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_name',
        'batch_date',
        'insurer_code',
        'total_processing_cost',
    ];

    public function insurer()
    {
        return $this->belongsTo(Insurer::class, 'insurer_code', 'code');
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
}
