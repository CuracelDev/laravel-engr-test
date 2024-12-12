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
        Schema::table('insurers', function (Blueprint $table) {
            $table->string('email')->unique();
            $table->string('batch_preference');
            $table->decimal('min_processing_cost_percentage', 5, 2)->default(10);
            $table->decimal('max_processing_cost_percentage', 5, 2)->default(15);
            $table->decimal('high_monetary_value', 9, 2)->default(50000);
            $table->integer('min_batch_size')->default(1);
            $table->integer('max_batch_size')->default(10);
            $table->integer('daily_capacity_limit')->default(100);
            $table->json('specialty_efficiency')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurers', function (Blueprint $table) {
           $table->dropColumn([
               'email',
               'min_processing_cost_percentage',
               'max_processing_cost_percentage',
               'min_batch_size',
               'max_batch_size',
               'daily_capacity_limit',
               'specialty_efficiency',
               'batch_preference',
               'high_monetary_value'
           ]);
           $table->dropSoftDeletes();
        });
    }
};
