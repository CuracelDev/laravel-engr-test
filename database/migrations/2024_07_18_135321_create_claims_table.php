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
            $table->unsignedBigInteger('insurer_id');
            $table->string('provider_name');
            $table->date('encounter_date');
            $table->string('specialty');
            $table->decimal('processing_cost', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->integer('batch_id');
             
            $table->unsignedTinyInteger('priority_level');
            $table->timestamps();

            
            $table->foreign('insurer_id')->references('id')->on('insurers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
}; 