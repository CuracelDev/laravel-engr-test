<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    protected $fillable = [
    'provider_id','insurer_id','encounter_date','submission_date',
    'priority_level','specialty','total_amount'
    ];

    /**
     * Each claim has many claim items
     */
    public function items() { return $this->hasMany(ClaimItem::class); }

    /**
     * Each claim belongs to one Batch
     */
    public function batch() { return $this->belongsTo(Batch::class); }

    /**
     * Each claim belongs to one provider
     */
    public function provider() { return $this->belongsTo(Provider::class); }

    /**
     * Each claim belongs to one insurer
     */
    public function insurer(){ return $this->belongsTo(Insurer::class); }
} 