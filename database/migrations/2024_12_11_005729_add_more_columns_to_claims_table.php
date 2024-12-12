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
        Schema::table('claims', function (Blueprint $table) {
            $table->string('provider_name');
            $table->string('insurer_code');
            $table->string('status')->default('pending');
            $table->date('encounter_date');
            $table->date('submission_date');
            $table->boolean('processed')->default(false);
            $table->date('processed_date');
            $table->string('specialty');
            $table->integer('priority_level');
            $table->decimal('total_claim_value', 9,2)->default(0);
            $table->decimal('time_of_month_cost_value', 9,2)->default(0);
            $table->decimal('specialty_type_cost_value', 9,2)->default(0);
            $table->decimal('priority_level_cost_value', 9,2)->default(0);
            $table->decimal('monetary_cost_value', 9,2)->default(0);
            $table->decimal('total_processing_cost_value', 9,2)->default(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn([
                'provider_name',
                'insurer_code',
                'encounter_date',
                'specialty',
                'priority_level',
                'total_value',
                'status'
            ]);

            $table->dropSoftDeletes();

        });
    }
};
