<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHoldStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hold_stock', function (Blueprint $table) {
            $table->id();
            $table->integer('avail_stock_id');
            $table->foreign('avail_stock_id')->references('id')->on('available_stock');
            $table->integer('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->integer('hold_qty');
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
        Schema::dropIfExists('hold_stock');
    }
}
