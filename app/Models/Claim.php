<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    protected $fillable = [
        'insurer_code',
        'provider_name',
        'encounter_date',
        'submission_date',
        'specialty',
        'priority_level',
        'total_value',
        'batch_id',
    ];

    public function insurer()
    {
        return $this->belongsTo(Insurer::class, 'insurer_code', 'code');
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