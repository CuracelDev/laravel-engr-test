<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurers', function (Blueprint $table) {
            $table->string('code')->primary(); 
            $table->string('name');
            $table->integer('daily_capacity'); 
            $table->integer('min_batch_size'); 
            $table->integer('max_batch_size'); 
            $table->string('preferred_date_type'); 
            $table->json('specialty_efficiency')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
}; 