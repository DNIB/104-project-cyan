<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_location', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('trip_id');
            $table->integer('location_id');
            $table->integer('arrival_method')->default(1);
            $table->dateTime('time')->nullable();
            $table->integer('trip_order')->default(-1);
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
        Schema::dropIfExists('trip_location');
    }
}
