<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('speciality');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
};
