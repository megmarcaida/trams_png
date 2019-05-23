<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vendor_code',100)->unique();
            $table->string('supplier_name',255);
            $table->string('delivery_type',100);
            $table->string('ordering_days',255);
            $table->string('module',255);
            $table->string('spoc_firstname',255);
            $table->string('spoc_middlename',255)->default('');
            $table->string('spoc_lastname',255);
            $table->string('spoc_contact_number',255);
            $table->string('spoc_email_address',255);
            $table->integer('status')->default(1)->nullable();
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
        Schema::dropIfExists('suppliers');
    }
}
