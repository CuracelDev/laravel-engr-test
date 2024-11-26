<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('code')->unique();
            $table->foreignUuid('provider_id')->constrained();
            $table->foreignUuid('insurer_id')->constrained();
            $table->date('encounter_date');
            $table->date('submission_date');
            $table->enum('priority_level',[1,2,3,4,5])->default(3);
            $table->foreignId('specialty_id')->constrained();
            $table->decimal('total_amount', 18, 2)->nullable();
            $table->enum('status', ['pending', 'batched', 'processed'])->default('pending');
            $table->decimal('processing_cost', 18, 2)->default(0.00);
            $table->decimal('moneytary_value', 18, 2)->default(0.00);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};