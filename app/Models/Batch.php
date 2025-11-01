<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'insurer_id','provider_name','batch_date','batch_code',
        'claims_count','total_amount','status','processed_on'
    ];

    protected $casts = [
        'batch_date'   => 'date',
        'processed_on' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function insurer(): BelongsTo { return $this->belongsTo(Insurer::class); }
    public function claims(): HasMany   { return $this->hasMany(Claim::class); }

    public static function makeCode(string $providerName, \Carbon\Carbon|string $date): string
    {
        $d = \Carbon\Carbon::parse($date);
        return sprintf('%s %s', $providerName, $d->format('M j Y'));
    }
}