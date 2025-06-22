<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'batches';
    protected $fillable = [
        'name', 'insurer_id', 'batch_date', 'total_cost'
    ];
    use HasFactory;

    public function claims() {
        return $this->hasMany(Claim::class);
    }

}
