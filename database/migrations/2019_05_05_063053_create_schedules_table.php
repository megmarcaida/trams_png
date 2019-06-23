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
            $table->date('recurrent_dateend')->nullable();
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
            $table->string('process_status',255)->nullable();
            $table->integer('isDocked')->default(0)->nullable();
            $table->datetime('gate_in_timestamp')->nullable();
            $table->integer('parking_timestamp')->nullable();
            $table->datetime('dock_in_timestamp')->nullable();
            $table->integer('unloading_timestamp')->nullable();
            $table->datetime('dock_out_timestamp')->nullable();
            $table->integer('egress_timestamp')->nullable();
            $table->datetime('gate_out_timestamp')->nullable();
            $table->integer('truck_turnaround_timestamp')->nullable();
            $table->string('recurrent_id')->nullable();
            $table->string('conflict_id')->nullable();
            $table->string('isDockChange')->nullable();
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
