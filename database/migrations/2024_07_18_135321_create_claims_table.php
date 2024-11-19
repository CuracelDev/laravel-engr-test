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
            $table->unsignedBigInteger('insurer_code');
            $table->foreign('insurer_code')->references('id')->on('insurers')->onDelete('cascade');
            $table->string('encounter_date');
            $table->string('total_value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
}; 