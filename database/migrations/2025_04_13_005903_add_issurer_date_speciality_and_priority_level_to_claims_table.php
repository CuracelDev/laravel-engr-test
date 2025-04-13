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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('insurer_id')->constrained('insurers')->onDelete('cascade');
            $table->date('date');
            $table->string('speciality');
            $table->enum('priority_level', [1, 2, 3, 4, 5])->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropForeign(['insurer_id']);
            $table->dropColumn('insurer_id');
            $table->dropColumn('date');
            $table->dropColumn('speciality');
            $table->dropColumn('priority_level');
        });
    }
};
