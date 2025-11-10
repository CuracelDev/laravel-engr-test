<?php

use App\Enums\ClaimStatus;
use App\Enums\PriorityLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurer_id')->constrained();
            $table->string('provider_name');
            $table->date('encounter_date');
            $table->date('submission_date')->default(now());
            $table->string('priority_level')->default(PriorityLevel::LEVEL_1)->index();
            $table->string('specialty')->index();
            $table->string('status')->default(ClaimStatus::PENDING);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('processing_cost', 12, 2)->default(0);

            $table->foreignId('batch_id')->nullable()->constrained();
            $table->timestamps();

            $table->index(['provider_name', 'encounter_date']);
            $table->index(['provider_name', 'submission_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
