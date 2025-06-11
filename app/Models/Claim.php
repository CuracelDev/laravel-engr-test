<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Claim
 *
 * @property int $id
 * @property int $insurer_id
 * @property string $provider_name
 * @property Carbon $encounter_date
 * @property string $specialty
 * @property int $priority_level
 * @property float $processing_cost
 * @property float $total_amount
 * @property int $batch_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClaimItem[] $items
 * @property-read \App\Models\Batch|null $batch
 *
 * @mixin \Eloquent
 */
class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    protected $fillable = [
        'insurer_id',
        'provider_name',
        'encounter_date',
        'specialty',
        'priority_level',
        'processing_cost',
        'total_amount',
        'batch_id'
    ];

    protected $casts = [
        'encounter_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(ClaimItem::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
