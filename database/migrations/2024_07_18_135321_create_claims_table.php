<?php

use App\Models\ClaimBatch;
use App\Models\Insurer;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'provider_id');
            $table->foreignIdFor(Insurer::class)->constrained();
            $table->decimal('total_amount', 10, 2);
            $table->date('encounter_date');
            $table->date('submission_date');
            $table->string('specialty');
            $table->tinyInteger('priority_level');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
}; 