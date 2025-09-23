<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_id');
            $table->string('name');
            $table->decimal('unit_price', 8, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->foreign('claim_id')->references('id')->on('claims')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_items');
    }
};
