<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('supplier_ids');
            $table->string('supplier_names',255);
            $table->string('logistics_company',255);
            $table->string('first_name',255);
            $table->string('last_name',255);
            $table->string('mobile_number',255)->default('');
            $table->string('company_id_number',255);
            $table->string('license_number',255);
            $table->datetime('dateOfSafetyOrientation')->nullable();
            $table->integer('isApproved')->default(0);
            $table->integer('status')->default(1);
            $table->softDeletes();
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
        Schema::dropIfExists('drivers');
    }
}
