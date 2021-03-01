<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetails1sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details1s', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->bigInteger('order_detail_id');
            $table->foreign('order_detail_id')->references('id')->on('order_details');
            $table->text('reason_status_change')->nullable();
            $table->float('total_weight', 11, 2)->nullable();
            $table->float('total_vol_weight', 11, 2)->nullable();
            $table->string('package_size', 100)->nullable();
            $table->float('package_length', 11, 2)->nullable();
            $table->float('package_width', 11, 2)->nullable();
            $table->float('package_height', 11, 2)->nullable();
            $table->string('tracking_no', 100)->nullable();
            $table->integer('read')->default(0);
            $table->bigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('order_details1s');
    }
}
