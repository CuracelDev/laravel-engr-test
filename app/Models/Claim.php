<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'insurer_id', 'provider_name', 'encounter_date',
        'submission_date', 'specialty', 'priority_level',
        'total_amount', 'batch_id'
    ];
    
    protected $guarded = [];  

    public function items()
    {
        return $this->hasMany(ClaimItem::class);
    }

    public function insurer()
    {
        return $this->belongsTo(Insurer::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

} 