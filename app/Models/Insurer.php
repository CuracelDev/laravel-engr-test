<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurer extends Model
{
    use HasFactory;

    protected $table = 'insurers';

    public function claims()
    {
        return $this->hasMany(Claim::class, 'insurer_code', 'id');
    }

    public function specialties()
    {
        return $this->hasMany(InsurerSpecialtyEfficiency::class, 'insurers_id', 'id');
    }
} 