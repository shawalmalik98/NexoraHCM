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
        if(!Schema::hasTable('tbl_attendance_devices')){
            Schema::create('tbl_attendance_devices', function (Blueprint $table) {
                $table->id('attendance_device_id');
                $table->integer('office_location_id');
                $table->string('device_name', 50);
                $table->string('device_port', 50);
                $table->longText('device_message');
                $table->integer('device_status');
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
        Schema::dropIfExists('tbl_attendance_devices');
    }
};
