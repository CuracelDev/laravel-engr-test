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
            $table->integer('min_batch_size');
            $table->integer('max_batch_size');
            $table->integer('daily_processing_capacity');
            $table->enum('date_preference', ['encounter', 'submission']);
            $table->json('specialty_factors')->nullable(); // specialty -> factor mapping
            $table->string('email');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
}; 