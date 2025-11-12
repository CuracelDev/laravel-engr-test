<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Claims Table Migration
 * 
 * Stores healthcare claims with batching information and processing status.
 * Claims are automatically grouped into batches for cost-optimized processing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            
            // Basic claim information
            $table->string('provider_name'); // Healthcare provider submitting the claim
            $table->foreignId('insurer_id')->constrained(); // Reference to insurers table
            $table->date('encounter_date'); // Date of medical service
            $table->date('submission_date'); // Date claim was submitted
            $table->integer('priority_level')->default(1); // Priority 1-5 (affects processing cost)
            $table->string('specialty'); // Medical specialty (cardiology, orthopedics, etc.)
            
            // Claim items and amount
            $table->json('items'); // Array of items: [{name, unit_price, quantity, subtotal}]
            $table->decimal('total_amount', 10, 2); // Total claim value (sum of all items)
            
            // Batching information
            $table->string('batch_id')->nullable(); // Batch identifier: "Provider Name + Date"
            $table->date('batch_date')->nullable(); // Date used for batching (encounter or submission)
            $table->decimal('processing_cost', 10, 2)->nullable(); // Calculated processing cost
            $table->boolean('processed')->default(false); // Whether batch has been processed
            
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['batch_id', 'batch_date']); // Fast batch lookups
            $table->index(['insurer_id', 'processed']); // Fast unprocessed claims queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
}; 