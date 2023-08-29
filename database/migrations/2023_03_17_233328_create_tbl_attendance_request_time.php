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
        
        if(!Schema::hasTable('tbl_attendance_request_time')){
            Schema::create('tbl_attendance_request_time', function (Blueprint $table) {
                $table->id('attendance_request_time_id');
                $table->integer('attendance_req_id');
                $table->integer('attendance_req_emp_id');
                $table->string('attendance_checkin_time', 50)->nullable();
                $table->string('attendance_checkout_time', 50)->nullable();
                $table->string('att_req_time_created_at', 255);

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
        Schema::dropIfExists('tbl_attendance_request_time');
    }
};
