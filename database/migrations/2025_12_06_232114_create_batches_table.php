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
            $table->foreignId('insurer_id')->constrained('insurers')->onDelete('cascade');
            $table->string('provider_name');
            $table->date('batch_date');
            $table->integer('claim_count')->default(0);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('processing_cost', 12, 2)->default(0.00);
            $table->enum('status', ['pending', 'notified', 'processing', 'completed'])->default('pending');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->unique(['insurer_id', 'provider_name', 'batch_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
