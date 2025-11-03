<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    /**
     * Get batches for the provider.
     */
    public function batches()
    {
        return $this->hasManyThrough(Batch::class, Claim::class);
    }
}
