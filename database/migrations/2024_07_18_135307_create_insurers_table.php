<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('insurers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('notify_email')->nullable();

            $table->unsignedInteger('daily_capacity')->default(1000);
            $table->unsignedInteger('min_batch_size')->default(1);
            $table->unsignedInteger('max_batch_size')->default(500);

            $table->enum('date_preference', ['encounter', 'submission'])->default('submission');

            $table->decimal('time_cost_min', 5, 4)->default(0.20);
            $table->decimal('time_cost_max', 5, 4)->default(0.50);
            $table->json('specialty_multipliers')->nullable();
            $table->json('priority_multipliers')->nullable();
            $table->decimal('value_cost_slope', 10, 6)->default(0.0000);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('insurers');
    }
};