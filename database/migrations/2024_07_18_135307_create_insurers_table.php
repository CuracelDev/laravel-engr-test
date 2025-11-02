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
            $table->string('email')->nullable();
            $table->integer('min_batch_size')->default(1);
            $table->integer('max_batch_size')->default(100);
            $table->integer('daily_capacity')->default(50);
            $table->enum('batch_by', ['encounter_date', 'submission_date'])->default('encounter_date');
            $table->json('specialty_efficiency')->nullable();
            $table->json('priority_multipliers')->nullable();
            $table->json('value_tiers')->nullable();
            $table->decimal('base_cost', 10, 2)->default(1.00);
            $table->decimal('monthly_cost_increase', 5, 2)->default(0.20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
}; 