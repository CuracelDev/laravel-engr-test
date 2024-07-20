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
        Schema::table('hmos', function (Blueprint $table) {
            $table->string('email');
            $table->string('batching_criteria')->default('encounter_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hmos', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('batching_criteria');
        });
    }
};
