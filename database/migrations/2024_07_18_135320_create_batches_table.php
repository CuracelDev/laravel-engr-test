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
           
            $table->string('hmo_code');
            $table->unsignedBigInteger('provider_id');
            $table->string('name');
            $table->enum('criteria_type', ['encounter', 'actual']);
            $table->timestamps();

           
            $table->unique(['hmo_code', 'name']);

            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
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
