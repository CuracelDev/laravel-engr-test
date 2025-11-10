<?php

use App\Enums\DatePreference;
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
            $table->string('email')->unique();
            $table->integer('min_batch_size')->default(1);
            $table->integer('max_batch_size')->default(100);
            $table->integer('daily_capacity_limit')->default(1000);
            $table->string('date_preference')->default(DatePreference::ENCOUNTER);
            $table->decimal('base_processing_cost', 10, 2)->default(3000.00);
            $table->decimal('time_of_month_multiplier_start', 5, 2)->default(0.20);
            $table->decimal('time_of_month_multiplier_end', 5, 2)->default(0.50);
            $table->jsonb('priority_cost_multipliers')->nullable();
            $table->jsonb('specialty_cost_multipliers')->nullable();
            $table->jsonb('claim_value_multipliers')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurers');
    }
};
