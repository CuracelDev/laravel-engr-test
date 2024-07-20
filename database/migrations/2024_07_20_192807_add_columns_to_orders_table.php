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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('hmo_code')->constrained('hmos', 'code');
            $table->string('provider_name')->after('id');
            $table->dateTime('encounter_date')->after('provider_name');
            $table->json('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['hmo_code']);
            $table->dropColumn(['hmo_code', 'provider_name', 'encounter_date']);
        });
    }
};
