<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('base_processing_cost', 10, 2)->default(10.00);
            $table->integer('daily_capacity')->default(100);
            $table->integer('min_batch_size')->default(1);
            $table->integer('max_batch_size')->default(50);
            $table->string('date_preference')->default('encounter'); // 'encounter' or 'submission'
            $table->json('specialty_efficiency_multipliers')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
}; 