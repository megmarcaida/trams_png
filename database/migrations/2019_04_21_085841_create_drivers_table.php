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
            $table->string('supplier_ids',255)->default('')->nullable();
            $table->string('supplier_names',255)->default('')->nullable();
            $table->string('logistics_company',255)->nullable();
            $table->string('first_name',255)->nullable();
            $table->string('last_name',255)->nullable();
            $table->string('full_name',255)->nullable();
            $table->string('mobile_number',255)->nullable()->default('');
            $table->string('company_id_number',255)->nullable();
            $table->string('license_number',100)->nullable();
            $table->date('dateOfSafetyOrientation')->nullable();
            $table->integer('isApproved')->default(0)->nullable();
            $table->integer('status')->default(1);
            $table->date('expirationDate')->nullable();
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
