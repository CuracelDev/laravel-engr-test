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
            $table->decimal('processing_cost', 10, 2);
            $table->integer('daily_capacity');
            $table->integer('min_batch_size');
            $table->integer('max_batch_size');
            $table->string('code')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
};
