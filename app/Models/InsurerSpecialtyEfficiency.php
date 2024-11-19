<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsurerSpecialtyEfficiency extends Model
{
    use HasFactory;

    public function insurer()
    {
        return $this->belongsTo(Insurer::class, 'insurers_id', 'id');
    }

    public function claims()
    {
        return $this->hasMany(Claim::class, 'specialty_id', 'specialty_id');
    }
}
