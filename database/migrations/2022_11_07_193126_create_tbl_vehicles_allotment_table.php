<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('tbl_vehicles_allotment')){
            Schema::create('tbl_vehicles_allotment', function (Blueprint $table) {
                $table->id('allotment_id');
                $table->string('allotment_vehicle_id', 255);
                $table->string('allotment_employee_id', 255);
                $table->string('allotment_vehicle_date', 255);
                $table->string('allotment_cancellation_date', 255)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_vehicles_allotment');
    }
};
