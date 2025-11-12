<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Insurers Table Migration
 * 
 * Stores insurer information and processing constraints for claim batching.
 * Each insurer has unique cost multipliers and batch size requirements.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Insurer company name
            $table->string('code')->unique(); // Unique identifier (e.g., INS-A)
            
            // Cost calculation multipliers
            $table->json('specialty_costs')->nullable(); // Cost multipliers by specialty (e.g., cardiology: 1.2)
            $table->json('priority_costs')->nullable(); // Cost multipliers by priority level (e.g., 5: 2.0)
            $table->decimal('value_cost_multiplier', 8, 4)->default(1.0001); // Cost per dollar of claim value
            
            // Processing constraints
            $table->integer('daily_capacity')->default(100); // Maximum claims processable per day
            $table->integer('min_batch_size')->default(1); // Minimum claims required to process batch
            $table->integer('max_batch_size')->default(50); // Maximum claims allowed in single batch
            
            // Batching preferences
            $table->enum('date_preference', ['encounter', 'submission'])->default('submission'); // Date to use for batching
            
            // Contact information
            $table->string('email'); // Email for batch processing notifications
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
}; 