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
            $table->json('specialty_efficiency'); // { "cardiology": 1.0, "orthopedics": 1.5 }
            $table->unsignedInteger('daily_capacity');
            $table->unsignedInteger('min_batch_size');
            $table->unsignedInteger('max_batch_size');
            $table->enum('batching_date_preference', ['encounter', 'submission']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
};
