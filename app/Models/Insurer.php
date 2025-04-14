<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Insurer extends Model
{
    use HasFactory;

    protected $table = 'insurers';

    /**
     * Get the user associated with the insurer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
