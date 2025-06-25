<?php

use App\Models\ClaimBatch;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->foreignIdFor(ClaimBatch::class)->nullable()->constrained();
            $table->dateTime('processed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['claim_batch_id']);
            $table->dropColumn(['claim_batch_id', 'processed_at']);
        });
    }
};
