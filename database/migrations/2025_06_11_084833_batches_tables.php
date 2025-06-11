<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            
            // Identifiers
            $table->string('provider_name');
            $table->unsignedBigInteger('insurer_id');
            $table->date('batch_date');

            // Optional tracking
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('total_processing_cost', 12, 2)->default(0);
            $table->unsignedInteger('claim_count')->default(0);

            // Meta flags
            $table->boolean('is_processed')->default(false);
            $table->timestamps();

            // Indexes & constraints
            $table->foreign('insurer_id')->references('id')->on('insurers')->onDelete('cascade');
            $table->unique(['provider_name', 'insurer_id', 'batch_date'], 'unique_batch_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
