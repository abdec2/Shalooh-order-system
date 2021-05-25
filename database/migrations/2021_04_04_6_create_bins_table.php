<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bins', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('bin_location', 50);
            $table->bigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->bigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->bigInteger('tag_number')->nullable();
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
        Schema::dropIfExists('bins');
    }
}
