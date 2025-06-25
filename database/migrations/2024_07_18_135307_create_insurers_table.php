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
            $table->string('email')->unique();
            $table->json('specialty_efficiencies')->nullable();
            $table->integer('daily_capacity');
            $table->integer('min_batch_size');
            $table->integer('max_batch_size');
            $table->enum('batch_date_preference', ['submission', 'encounter']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
}; 