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
            $table->string('provider_name');
            $table->string('insurer_code');
            $table->date('batch_date');
            $table->string('batch_identifier')->unique();
            $table->integer('claims_count')->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('processing_cost', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['insurer_code', 'batch_date']);
            $table->index(['provider_name', 'batch_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
