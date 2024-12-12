<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Claim extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'claims';

    protected $fillable = [
        'provider_name',
        'insurer_code',
        'status',
        'encounter_date',
        'submission_date',
        'processed',
        'processed_date',
        'specialty',
        'priority_level',
        'total_claim_value',
        'time_of_month_cost_value',
        'specialty_type_cost_value',
        'priority_level_cost_value',
        'monetary_cost_value',
        'total_processing_cost_value',
    ];

    public function insurer()
    {
        return $this->belongsTo(Insurer::class, 'insurer_code', 'code');
    }

    public function items()
    {
        return $this->hasMany(ClaimItem::class);
    }

}
