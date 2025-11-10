<?php

use App\Enums\BatchStatus;
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
            $table->string('batch_identifier')->unique();
            $table->foreignId('insurer_id')->constrained();
            $table->string('provider_name');
            $table->date('batch_date');
            $table->string('status')->default(BatchStatus::PENDING);
            $table->integer('total_claims')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->decimal('total_processing_cost', 15, 2)->default(0.00);
            $table->date('processing_date');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
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
