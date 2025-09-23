<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Insurer extends Model
{
    use HasFactory;

    protected $table = 'insurers';

    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * claims for the insurer
     */
    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'insurer_code', 'code');
    }

    /**
     * batches for the insurer
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, 'insurer_code', 'code');
    }

    /**
     * configuration for the insurer
     */
    public function configuration(): HasOne
    {
        return $this->hasOne(InsurerConfiguration::class, 'insurer_code', 'code');
    }

    /**
     * check if insurer can process more claims today
     */
    public function canProcessMoreClaims(int $additionalClaims = 1): bool
    {
        if (! $this->configuration) {
            return true;
        }

        $todaysClaims = $this->claims()
            ->whereDate('created_at', today())
            ->count();

        return ($todaysClaims + $additionalClaims) <= $this->configuration->daily_capacity;
    }

    public function getAvailableCapacity(): int
    {
        if (! $this->configuration) {
            return PHP_INT_MAX;
        }

        $todaysClaims = $this->claims()
            ->whereDate('created_at', today())
            ->count();

        return max(0, $this->configuration->daily_capacity - $todaysClaims);
    }
}
