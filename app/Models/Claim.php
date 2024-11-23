<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    protected $fillable = [
        'insurer_id',
        'encounter_date',
        'submission_date',
        'priority_level',
        'specialty_type',
        'monetary_value',
        'items',
    ];

    public function insurer()
    {
        return $this->belongsTo(Insurer::class);
    }
}
