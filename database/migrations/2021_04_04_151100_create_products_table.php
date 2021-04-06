<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('sku')->unique();
            $table->integer('parent');
            $table->bigInteger('stock_qty');
            $table->string('image_path', 255);
            $table->integer('bin_id')->nullable();
            $table->foreign('bin_id')->references('id')->on('bins');
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
        Schema::dropIfExists('products');
    }
}
