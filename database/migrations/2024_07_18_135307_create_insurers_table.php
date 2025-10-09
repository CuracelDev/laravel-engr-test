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
            $table->string('code')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->json('specialty_efficiency')->nullable(); 
            $table->json('priority_cost_multiplier')->nullable();
            $table->integer('daily_capacity')->default(1000);
            $table->integer('min_batch_size')->default(1);
            $table->integer('max_batch_size')->default(500);
            $table->enum('batch_date_pref', ['encounter','submission'])->default('submission');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
}; 