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
            $table->string('email');
            $table->integer('daily_capacity')->default(100);
            $table->integer('min_batch_size')->default(5);
            $table->integer('max_batch_size')->default(50);
            $table->enum('date_preference', ['encounter', 'submission'])->default('encounter');
            $table->json('specialty_efficiency')->nullable();
            $table->decimal('base_processing_cost', 10, 2)->default(100.00);
        });
    }

    public function down(): void
    {
        Schema::table('insurers', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'daily_capacity',
                'min_batch_size',
                'max_batch_size',
                'date_preference',
                'specialty_efficiency',
                'base_processing_cost'
            ]);
        });
    }
};
