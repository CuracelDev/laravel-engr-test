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
        Schema::create('insurer_specialty_efficiencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specialty_id');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');
            $table->unsignedBigInteger('insurers_id');
            $table->foreign('insurers_id')->references('id')->on('insurers')->onDelete('cascade');
            $table->enum('efficiency',['0','1','2'])->default('0')->comment('Efficiency levels: 0 = Low, 1 = Medium, 2 = High');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurer_specialty_efficiencies');
    }
};
