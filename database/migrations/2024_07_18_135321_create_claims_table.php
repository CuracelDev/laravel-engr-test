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
            $table->string('insurer_code');
            $table->date('encounter_date');
            $table->date('submission_date');
            $table->integer('priority_level');
            $table->string('specialty');
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->timestamps();

            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('set null');
            $table->index(['provider_name', 'encounter_date']);
            $table->index(['insurer_code', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
