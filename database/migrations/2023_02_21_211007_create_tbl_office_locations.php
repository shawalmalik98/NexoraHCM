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
        if(!Schema::hasTable('tbl_office_locations')){
            Schema::create('tbl_office_locations', function (Blueprint $table) {
                $table->id('location_id');
                $table->string('location_title', 255);
                $table->string('location_alt_id', 50)->nullable();
                $table->longtext('location_address');
                $table->string('location_latitude', 255);
                $table->string('location_longitude', 255);
                $table->integer('location_attendance_radius')->comment('Value is in meters');
                $table->string('location_added_at',255);
                $table->string('location_updated_at',255)->nullable();
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
        Schema::dropIfExists('tbl_office_locations');
    }
};
