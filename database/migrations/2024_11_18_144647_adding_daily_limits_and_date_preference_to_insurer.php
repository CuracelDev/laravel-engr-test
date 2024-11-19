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
            $table->integer('daily_limits')->nullable();
            $table->string('maximum_num_of_batch')->nullable();
            $table->string('minimum_num_of_batch')->nullable();
            $table->decimal('total_processing_cost', 10, 2)->comment('The overall proccessing cost per insurer');
            $table->string('date_preference')->nullable()->comment('different preference on using either the encounter or submission date for batching');
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->enum('priority',["1","2","3","4","5"])->default("1")->comment('ranging from 0 to 5');
            $table->unsignedBigInteger('specialty_id');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');
            $table->unsignedBigInteger('efficiency_id');
            $table->foreign('efficiency_id')->references('id')->on('insurer_specialty_efficiencies')->onDelete('cascade');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('batched_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurers', function (Blueprint $table) {
            $table->dropColumn(['daily_limits', 'maximum_num_of_batch', 'minimum_num_of_batch','date_preference','total_processing_cost']);
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['priority', 'specialty_id','batched_at','processed_at', 'efficiency_id']);
        });
    }
};
