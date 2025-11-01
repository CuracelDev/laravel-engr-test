<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurer_id')->constrained()->cascadeOnDelete();

            $table->string('provider_name');

            $table->date('batch_date');

            $table->string('batch_code')->unique();

            $table->unsignedInteger('claims_count')->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);

            $table->enum('status', ['queued', 'processed', 'failed'])->default('queued');
            $table->date('processed_on')->nullable();

            $table->timestamps();

            $table->index(['insurer_id', 'provider_name', 'batch_date']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('batches');
    }
};