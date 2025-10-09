<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurer_id')->constrained('insurers')->onDelete('cascade');  // Foreign key linking to insurers table
            $table->string('provider_name');
            $table->date('encounter_date');
            $table->string('specialty');
            $table->integer('priority_level')->default(1);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
        // Schema::dropIfExists('claim_items');
    }
};
