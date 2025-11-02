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
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
            $table->string('provider_name');
            $table->string('provider_email');
            $table->string('claim_reference_code')->unique()->nullable();
            $table->date('encounter_date');
            $table->date('submission_date')->default(now());
            $table->string('specialty');
            $table->enum('priority_level', ['low', 'medium', 'high'])->default('medium');
            $table->decimal('claim_total', 10, 2)->default(0);
            $table->enum('status', ['pending', 'processed', 'notified'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
}; 