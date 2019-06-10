<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDockUnavailabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dock__unavailabilities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('dock_id',255)->nullable();
            $table->string('dock_name',255)->nullable();
            $table->datetime('date_of_unavailability')->nullable();
            $table->string('recurrence',255)->nullable();
            $table->string('emergency',255)->nullable();
            $table->string('ordering_days',255)->nullable();
            $table->string('time',255)->nullable();
            $table->string('type',255)->nullable();
            $table->text('reason')->nullable();
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
        Schema::dropIfExists('dock_unavailability');
    }
}
