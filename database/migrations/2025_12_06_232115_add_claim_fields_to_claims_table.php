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
            $table->foreignId('insurer_id')->constrained('insurers')->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
            $table->string('provider_name');
            $table->date('encounter_date');
            $table->date('submission_date');
            $table->integer('priority_level')->default(3);
            $table->string('specialty');
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->enum('status', ['pending', 'batched', 'processed'])->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign(['insurer_id']);
            $table->dropForeign(['batch_id']);
            $table->dropColumn([
                'insurer_id',
                'batch_id',
                'provider_name',
                'encounter_date',
                'submission_date',
                'priority_level',
                'specialty',
                'total_amount',
                'status'
            ]);
        });
    }
};
