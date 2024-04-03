<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('tbl_attendance')){
            Schema::create('tbl_attendance', function (Blueprint $table) {
                $table->id('attendance_id');
                $table->integer('att_employee_id');
                $table->string('att_check_in_time', 50);
                $table->string('att_check_out_time', 50)->nullable();
                $table->integer('att_check_in_status');
                $table->integer('att_check_out_status')->nullable();
                $table->string('att_check_overall_status', 50)->nullable();
                $table->string('att_shift_start_time', 50)->nullable();
                $table->string('att_shift_end_time', 50)->nullable();
                $table->string('att_comp_date', 50)->nullable();
                $table->string('att_for_date', 50);
                $table->string('att_for_month', 50);
                $table->string('att_for_year', 50);
                $table->string('att_ci_lat', 50)->nullable();
                $table->string('att_ci_long', 50)->nullable();
                $table->string('att_co_lat', 50)->nullable();
                $table->string('att_co_long', 50)->nullable();
                $table->integer('sap_ci_flag')->nullable();
                $table->integer('sap_co_flag')->nullable();
                $table->datetime('sap_ci_timestamp')->nullable();
                $table->datetime('sap_co_timestamp')->nullable();
                $table->string('att_ci_marked_time', 50)->nullable();
                $table->string('att_co_marked_time', 50)->nullable();
                $table->integer('att_remote_applicable')->default(0)->comment('0 = Attendence 1 = WFH');
                $table->integer('att_request_approval')->nullable()->comment('1 = Request Approval Attendance 2 = Request Approval Leave');
                $table->integer('att_check_in_source')->default(1)->comment('1 = Web Application , 2 = Mobile Application, 3 = Update Attendance, 4 = Biometric Machine');
                $table->integer('att_check_out_source')->nullable()->comment('1 = Web Application , 2 = Mobile Application, 3 = Update Attendance, 4 = Biometric Machine');
                $table->integer('att_updated_by')->nullable();
                $table->string('att_updated_remarks', 255)->nullable();
                $table->timestamp('att_created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->string('att_updated_at',50)->nullable();
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
        Schema::dropIfExists('tbl_attendance');
    }
};
