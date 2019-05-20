<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParkingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('parking_name',255)->nullable();
            $table->string('parking_description',255)->nullable();
            $table->string('parking_slot',255)->nullable();
            $table->string('parking_area',255)->nullable();
            $table->string('parking_block',255)->nullable();
            $table->string('parking_status',255)->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('parking');
    }
}
