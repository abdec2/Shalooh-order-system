<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_number');
            $table->integer('shipping_carrier_id');
            $table->foreign('shipping_carrier_id')->references('id')->on('shipping_carrier');
            $table->dateTime('order_date', $precision = 0);
            $table->string('payment_method', 100);
            $table->integer('order_status_id');
            $table->foreign('order_status_id')->references('id')->on('order_status');
            $table->string('shipping_address', 1000);
            $table->string('tracking_no', 255)->nullable();       
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
        Schema::dropIfExists('orders');
    }
}
