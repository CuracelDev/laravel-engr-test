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
            $table->foreignId('insurer_id')->constrained('insurers')->onDelete('cascade');
            $table->string('batch_code')->unique();
            $table->date('batch_date');
            $table->integer('claim_count')->default(0);
            $table->decimal('total_value', 12, 2)->default(0);
            $table->decimal('processing_cost', 10, 2)->default(0);
            $table->boolean('notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
