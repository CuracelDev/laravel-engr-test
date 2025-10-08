<?php

namespace App\Models;

use App\Models\User;
use App\Models\Insurer;
use App\Models\ClaimItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    protected $fillable = [
        'insurer_id',
        'provider_name',
        'encounter_date',
        'specialty',
        'priority_level',
        'total_amount',
    ];

    public function items()
    {
        return $this->hasMany(ClaimItem::class);
    }

    public function insurer()
    {
        return $this->belongsTo(Insurer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
