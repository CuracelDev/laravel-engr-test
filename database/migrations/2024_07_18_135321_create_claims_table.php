<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained()->nullOnDelete();

            $table->string('provider_name');

            $table->date('encounter_date');
            $table->date('submission_date');
            $table->string('specialty');
            $table->unsignedTinyInteger('priority_level');
            $table->unsignedInteger('items_count')->default(0);

            $table->decimal('total_value', 14, 2);

            $table->decimal('processing_cost', 14, 2)->nullable();

            $table->enum('status', ['pending', 'batched', 'processed'])->default('pending');

            $table->timestamps();

            $table->index(['insurer_id']);
            $table->index(['provider_name']);
            $table->index(['encounter_date', 'submission_date']);
            $table->index(['priority_level']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('claims');
    }
};