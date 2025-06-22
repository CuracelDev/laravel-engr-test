<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'notification_email','specialty_efficiency', 'daily_capacity', 'minimum_batch_size', 'maximum_batch_size', 'batching_date_preference'
    ];

    protected $table = 'insurers';

    protected $casts = [
        'specialty_efficiency' => 'array',
    ];

    public function claims() {
        return $this->hasMany(Claim::class);
    }

}
