<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name');
            $table->foreignId('insurer_id')->constrained('insurers');
            $table->date('encounter_date');
            $table->date('submission_date');
            $table->integer('priority'); // 1-5
            $table->string('specialty');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'batched'])->default('pending');
            $table->unsignedBigInteger('batch_id')->nullable(); // Linked later
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
}; 