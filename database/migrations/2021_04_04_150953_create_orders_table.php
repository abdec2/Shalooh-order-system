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
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('order_number');
            $table->string('customer_name', 255);
            $table->string('customer_contact', 50);
            $table->text('order_data')->nullable();
            $table->bigInteger('shipping_carrier_id')->nullable();
            $table->foreign('shipping_carrier_id')->references('id')->on('shipping_carrier');
            $table->dateTime('order_date', $precision = 0);
            $table->string('payment_method', 100);
            $table->bigInteger('order_status_id');
            $table->foreign('order_status_id')->references('id')->on('order_status');
            $table->string('shipping_address1', 1000);
            $table->string('shipping_address2', 1000)->nullable();
            $table->string('city', 255);
            $table->string('state', 255)->nullable();
            $table->string('postal', 255)->nullable();
            $table->string('country', 255);
            $table->string('tracking_no', 255)->nullable();
            $table->integer('total_weight')->nullable();
            $table->float('total_vol_weight', 11 ,2)->nullable();
            $table->string('package_size', 100)->nullable();
            $table->float('package_length', 11, 2)->nullable();
            $table->float('package_width', 11, 2)->nullable();
            $table->float('package_height', 11, 2)->nullable();
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
