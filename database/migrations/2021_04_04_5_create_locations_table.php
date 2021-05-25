<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('location', 30);
            $table->bigInteger('location_category_id');
            $table->foreign('location_category_id')->references('id')->on('location_categories');
            $table->integer('total_bins')->nullable();
            $table->integer('bins_in_use')->nullable();
            $table->bigInteger('bin_init');
            $table->bigInteger('bin_ending');

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
        Schema::dropIfExists('locations');
    }
}
