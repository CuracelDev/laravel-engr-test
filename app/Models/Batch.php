<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        "date",
        "insurer_id",
        "name",
    ];

    const STATUS_PROCESSED = 'processed';
    const STATUS_PENDING = 'pending';

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
}
