<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('po_number',255)->nullable();
            $table->string('dock_id',255)->nullable();
            $table->string('dock_name',255)->nullable();
            $table->date('date_of_delivery')->nullable();
            $table->string('recurrence',255)->nullable();
            $table->string('ordering_days',255)->nullable();
            $table->string('slotting_time',255)->nullable();
            $table->string('truck_id',255)->nullable();
            $table->string('container_number',255)->nullable();
            $table->string('supplier_id',255)->nullable();
            $table->string('driver_id',255)->nullable();
            $table->string('assistant_id',255)->nullable();
            $table->text('reason')->nullable();
            $table->string('material_list',255)->nullable();
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
        Schema::dropIfExists('schedules');
    }
}
