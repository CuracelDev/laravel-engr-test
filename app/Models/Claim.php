<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    /**
     * Define a one-to-many relationship with Item.
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function insurer()
    {
        return $this->belongsTo(Insurer::class, 'insurer_code', 'id');
    }

    public function specialtyEfficiency()
    {
        return $this->hasOne(InsurerSpecialtyEfficiency::class,'id', 'efficiency_id');
    }
} 