<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Batch
 *
 * @property int $id
 * @property string $provider_name
 * @property int $insurer_id
 * @property Carbon $batch_date
 * @property float $total_amount
 * @property float $total_processing_cost
 * @property int $claim_count
 * @property bool $is_processed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Claim[] $claims
 * @property-read \App\Models\Insurer $insurer
 *
 * @mixin \Eloquent
 */
class Batch extends Model
{
    use HasFactory;

    protected $table = 'batches';

    protected $fillable = [
        'provider_name',
        'insurer_id',
        'batch_date',
        'total_amount',
        'total_processing_cost',
        'claim_count',
        'is_processed',
    ];

    protected $casts = [
        'batch_date' => 'date',
        'is_processed' => 'boolean',
    ];

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function insurer()
    {
        return $this->belongsTo(Insurer::class);
    }
}
