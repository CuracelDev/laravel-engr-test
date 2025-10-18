<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique(); // Provider Name + Date
            $table->foreignId('insurer_id')->constrained('insurers')->onDelete('cascade');
            $table->date('batch_date');
            $table->integer('total_claims')->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending, ready, notified, processed
            $table->timestamp('optimized_at')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            
            $table->index(['insurer_id', 'batch_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};

