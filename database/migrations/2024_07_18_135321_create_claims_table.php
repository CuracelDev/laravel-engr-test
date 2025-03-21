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
            $table->string('insurer_code'); 
            $table->string('provider_name');
            $table->date('encounter_date');
            $table->date('submission_date')->default(now()); 
            $table->string('specialty');
            $table->tinyInteger('priority_level')->unsigned(); 
            $table->decimal('total_value', 10, 2); 
            $table->unsignedBigInteger('batch_id')->nullable(); 
            $table->timestamps();

            $table->foreign('insurer_code')->references('code')->on('insurers');
            $table->foreign('batch_id')->references('id')->on('batches');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
}; 