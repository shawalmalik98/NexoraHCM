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
    
        if(!Schema::hasTable('tbl_attendance_report')){
            Schema::create('tbl_attendance_report', function (Blueprint $table) {
                $table->id('att_report_id');
                $table->string('att_report_emp_id',500);
                $table->string('att_report_month', 255);
                $table->string('att_report_year', 255);
                $table->integer('att_report_created_by');
                $table->string('att_report_created_at',50);
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
        Schema::dropIfExists('tbl_attendance_report');
    }
};
