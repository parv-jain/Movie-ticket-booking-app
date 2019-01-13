<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_info', function (Blueprint $table) {
            $table->increments('seat_info_id');
            $table->string('screen');
            $table->string('row');
            $table->integer('noOfSeats');
            $table->string('aisleSeats');
            $table->string('reservedSeats');
            $table->softDeletesTz();
            $table->timestampsTz();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat_info');
    }
}
