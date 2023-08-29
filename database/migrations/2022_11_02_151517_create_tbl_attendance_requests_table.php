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
        if(!Schema::hasTable('tbl_attendance_requests')){
            Schema::create('tbl_attendance_requests', function (Blueprint $table) {
                $table->id('attendance_request_id');
                $table->string('request_employee_id', 50);
                $table->string('request_attendance_date', 50);
                $table->string('request_attendance_status', 50);
                $table->longText('request_attendance_message');
                $table->integer('request_approved_by_hr')->comment('1 = Pending, 2 = Approved, 3 = Disapproved');
                $table->string('request_approved_date_hr', 255)->nullable();
                $table->string('request_submition_date', 50);
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
        Schema::dropIfExists('tbl_attendance_requests');
    }
};
