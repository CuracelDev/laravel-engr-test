<?php

namespace App\Models;

use App\Models\Insurer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\Claims\NewBatchAvailableNotification;

class ClaimBatch extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function (ClaimBatch $batch) {
            $batch->provider->notify(new NewBatchAvailableNotification($batch));
        });
    }

    protected $guarded = ['id'];

    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
