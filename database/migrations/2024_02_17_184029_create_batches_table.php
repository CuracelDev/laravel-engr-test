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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name');
            $table->date('batch_date'); 
            $table->string('insurer_code'); 
            $table->decimal('total_processing_cost', 10, 2)->default(0); 
            $table->timestamps();

            $table->foreign('insurer_code')->references('code')->on('insurers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
