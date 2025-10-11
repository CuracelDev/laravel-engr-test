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
            $table->foreignId('insurer_id')->constrained();
            $table->string('provider_name');
            $table->date('encounter_date');
            $table->date('submission_date')->default(DB::raw('CURRENT_DATE'));
            $table->string('specialty');
            $table->unsignedTinyInteger('priority_level'); // 1 to 5
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('batch_id')->nullable()->constrained();
            $table->timestamps();
        });

        Schema::create('claim_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2); // unit_price * quantity
            $table->timestamps();
        });

        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurer_id')->constrained();
            $table->string('provider_name');
            $table->date('batch_date'); // Insurer processes yesterday's batch
            $table->decimal('total_value', 10, 2)->default(0);
            $table->enum('status', ['pending', 'processed'])->default('pending');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
        Schema::dropIfExists('claim_items');
        Schema::dropIfExists('batches');
    }
}; 