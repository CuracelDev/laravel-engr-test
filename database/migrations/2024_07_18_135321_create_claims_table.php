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
            $table->date('submission_date');
            $table->string('specialty');
            $table->unsignedTinyInteger('priority_level');
            $table->decimal('total_amount', 12, 2);
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
