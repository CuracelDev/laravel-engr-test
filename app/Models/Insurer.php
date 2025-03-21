<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurer extends Model
{
    use HasFactory;

    protected $table = 'insurers';

    protected $primaryKey = 'code'; 
    public $incrementing = false; 
    protected $keyType = 'string'; 

    protected $fillable = [
        'code',
        'name',
        'daily_capacity',
        'min_batch_size',
        'max_batch_size',
        'preferred_date_type',
        'specialty_efficiency',
    ];

    public function claims()
    {
        return $this->hasMany(Claim::class, 'insurer_code', 'code');
    }
    
    public function batches()
    {
        return $this->hasMany(Batch::class, 'insurer_code', 'code');
    }
} 