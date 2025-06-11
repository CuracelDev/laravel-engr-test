<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClaimItem
 *
 * @property int $id
 * @property int $claim_id
 * @property string $name
 * @property int $quantity
 * @property float $unit_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Claim $claim
 *
 * @mixin \Eloquent
 */
class ClaimItem extends Model
{
    use HasFactory;

    protected $table = 'claim_items';

    protected $fillable = ['claim_id', 'name', 'quantity', 'unit_price'];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }
}
