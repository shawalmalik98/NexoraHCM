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
        if(!Schema::hasTable('tbl_vehicles_images')){
            Schema::create('tbl_vehicles_images', function (Blueprint $table) {
                $table->id('vehicle_img_id');
                $table->integer('vehicle_img_vehicle_id');
                $table->string('vehicle_img_name', 1000);
                $table->string('vehicle_img_upload_date', 255);
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
        Schema::dropIfExists('tbl_vehicles_images');
    }
};
