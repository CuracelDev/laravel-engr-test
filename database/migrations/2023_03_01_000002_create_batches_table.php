<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->foreignId('insurer_id')->constrained()->onDelete('cascade');
            $table->date('batch_date');
            $table->decimal('total_monetary_value', 10, 2);
            $table->timestamps();
        });

        Schema::create('batch_claim', function (Blueprint $table) {
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->foreignId('claim_id')->constrained()->onDelete('cascade');
            $table->primary(['batch_id', 'claim_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batch_claim');
        Schema::dropIfExists('batches');
    }
}
