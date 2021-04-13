<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHurtStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hurt_stock', function (Blueprint $table) {
            $table->id();
            $table->integer('bin_id');
            $table->foreign('bin_id')->references('id')->on('bins');
            $table->integer('hurt_qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hurt_stock');
    }
}
