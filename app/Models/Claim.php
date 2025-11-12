<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Claim Model
 * 
 * Represents a healthcare claim submitted by a provider to an insurer.
 * Claims are automatically grouped into batches for cost-optimized processing.
 * 
 * @property int $id
 * @property string $provider_name Healthcare provider name
 * @property int $insurer_id Foreign key to insurers table
 * @property Carbon $encounter_date Date of medical service
 * @property Carbon $submission_date Date claim was submitted
 * @property int $priority_level Priority 1-5 (affects processing cost)
 * @property string $specialty Medical specialty (cardiology, orthopedics, etc.)
 * @property array $items Array of claim items with prices and quantities
 * @property float $total_amount Total claim value
 * @property string|null $batch_id Batch identifier (Provider Name + Date)
 * @property Carbon|null $batch_date Date used for batching
 * @property float|null $processing_cost Calculated processing cost
 * @property bool $processed Whether batch has been processed
 */
class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_name', 'insurer_id', 'encounter_date', 'submission_date',
        'priority_level', 'specialty', 'items', 'total_amount', 'batch_id',
        'batch_date', 'processing_cost', 'processed'
    ];

    protected $casts = [
        'encounter_date' => 'date',
        'submission_date' => 'date',
        'batch_date' => 'date',
        'items' => 'array',
        'total_amount' => 'decimal:2',
        'processing_cost' => 'decimal:2',
        'processed' => 'boolean',
    ];

    /**
     * Get the insurer for this claim
     */
    public function insurer(): BelongsTo
    {
        return $this->belongsTo(Insurer::class);
    }

    /**
     * Get the date to use for batching based on insurer preference
     * 
     * @return Carbon Either encounter_date or submission_date
     */
    public function getBatchDateForInsurer(): Carbon
    {
        return $this->insurer->date_preference === 'encounter' 
            ? $this->encounter_date 
            : $this->submission_date;
    }

    /**
     * Generate batch ID in format: "Provider Name Month Day Year"
     * Example: "Provider A Jan 15 2024"
     * 
     * @return string Batch identifier
     */
    public function generateBatchId(): string
    {
        $batchDate = $this->getBatchDateForInsurer();
        return $this->provider_name . ' ' . $batchDate->format('M j Y');
    }
} 