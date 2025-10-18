<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurer_id')->constrained('insurers')->onDelete('cascade');
            $table->string('provider_name');
            $table->date('encounter_date');
            $table->date('submission_date');
            $table->integer('priority_level')->default(1); // 1-5
            $table->string('specialty');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
            $table->decimal('processing_cost', 12, 2)->nullable();
            $table->string('status')->default('pending'); // pending, batched, processed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
}; 