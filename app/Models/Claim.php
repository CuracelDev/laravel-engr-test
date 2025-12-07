<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_name',
        'insurer_id',
        'encounter_date',
        'submission_date',
        'priority',
        'specialty',
        'amount',
        'status',
        'batch_id',
    ];

    public function insurer()
    {
        return $this->belongsTo(Insurer::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function items()
    {
        return $this->hasMany(ClaimItem::class);
    }
}