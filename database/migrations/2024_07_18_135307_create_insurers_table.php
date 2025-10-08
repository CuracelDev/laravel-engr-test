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
            $table->integer('min_batch_size')->default(1);  // Minimum batch size
            $table->integer('max_batch_size')->default(100); // Maximum batch size
            $table->json('processing_costs')->nullable(); // Store processing costs, potentially by day of the month or specialty
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
};
