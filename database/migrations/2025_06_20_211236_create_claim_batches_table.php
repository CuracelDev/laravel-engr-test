<?php

use App\Models\User;
use App\Models\Claim;
use App\Models\Insurer;
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
        Schema::create('claim_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Insurer::class)->constrained();
            $table->foreignIdFor(User::class, 'provider_id');
            $table->string('key')->unique();
            $table->decimal('processing_cost', 10, 2);
            $table->date('batch_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_batches');
    }
};
