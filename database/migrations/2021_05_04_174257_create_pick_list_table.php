<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pick_list', function (Blueprint $table) {
            $table->id();
            $table->integer('order_ass_user_id');
            $table->foreign('order_ass_user_id')->references('id')->on('order_assigned_users');
            $table->integer('location_id');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->integer('bin_id');
            $table->foreign('bin_id')->references('id')->on('bins');
            $table->integer('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer('qty_picked');
            $table->string('status', 10);
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
        Schema::dropIfExists('pick_list');
    }
}