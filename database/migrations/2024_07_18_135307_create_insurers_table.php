<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('insurers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();

            $table->json('specialty_type')->nullable(); // {specialty: efficiency_factor}
            // $table->json('priority_level')->nullable();   // {priority: cost_level}
            $table->integer('daily_capacity')->default(1000);
            $table->integer('min_batch_size')->default(10);
            $table->integer('max_batch_size')->default(100);
            $table->enum('batch_by', ['encounter', 'submission'])->default('submission');
            // $table->decimal('base_processing_cost', 8, 2)->default(10.00);
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
};
