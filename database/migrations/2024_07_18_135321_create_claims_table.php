<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name');
            $table->foreignId('insurer_id')->constrained();
            $table->date('encounter_date');
            $table->date('submission_date');
            $table->string('specialty');
            $table->integer('priority_level'); // 1-5
            $table->decimal('total_amount', 10, 2);
            $table->string('batch_id')->nullable();
            $table->timestamps();

            $table->index(['provider_id', 'insurer_id']);
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
