<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Batch extends Model
{
    protected $fillable = [
        'name',
        'insurer_id',
        'batch_date',
        'total_monetary_value',
    ];

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    public function claims(): BelongsToMany
    {
        return $this->belongsToMany(Claim::class);
    }
}