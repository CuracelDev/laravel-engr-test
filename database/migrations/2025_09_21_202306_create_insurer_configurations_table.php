<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurer_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('insurer_code');
            $table->integer('daily_capacity')->default(100);
            $table->integer('min_batch_size')->default(1);
            $table->integer('max_batch_size')->default(50);
            $table->enum('date_preference', ['encounter', 'submission'])->default('encounter');
            $table->json('specialty_efficiency');
            $table->decimal('priority_multiplier', 3, 2)->default(1.00);
            $table->decimal('value_multiplier', 5, 4)->default(0.0001);
            $table->timestamps();

            $table->foreign('insurer_code')->references('code')->on('insurers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurer_configurations');
    }
};
