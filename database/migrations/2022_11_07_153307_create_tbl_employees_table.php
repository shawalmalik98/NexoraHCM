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
        if(!Schema::hasTable('tbl_employees')){
            Schema::create('tbl_employees', function (Blueprint $table) {
                $table->id('employee_id');
                $table->integer('employee_code');
                $table->string('employee_name', 255);
                $table->string('employee_contactno', 255);
                $table->string('employee_personal_email', 255);
                $table->string('employee_work_email', 255)->nullable();
                $table->string('employee_cnic', 50)->nullable();
                $table->string('cnic_issue_date', 50)->nullable();
                $table->string('cnic_expiry_date', 50)->nullable();
                $table->string('employee_gender', 10)->nullable();
                $table->string('employee_doj', 25);
                $table->string('employee_status', 255);
                $table->integer('employee_designation');
                $table->integer('employee_department');
                $table->integer('employee_company')->default(1);
                $table->integer('employee_group')->default(1);
                $table->string('business_unit', 255)->nullable();
                $table->string('employee_jobtitle', 255);
                $table->string('employee_offdays', 255)->nullable();
                $table->string('employee_shift_flexibilty', 5)->nullable();
                $table->boolean('employee_geofencing_enability')->nullable();
                $table->string('employee_password', 1000);
                $table->integer('employee_senior_manager');
                $table->integer('employee_junior_manager')->nullable();
                $table->integer('employee_is_manager');
                $table->integer('employee_isactive');
                $table->string('employee_deactivate_date', 50)->nullable();
                $table->string('employee_jobtype', 50);
                $table->integer('employee_location')->nullable();
                $table->integer('employee_web_access')->nullable()->comment('0 = deactive  1 = active');
                $table->integer('employee_mobile_access')->nullable()->comment('0 = deactive 1 = active');
                $table->string('employee_grade', 10)->default(1);
                $table->longText('employee_fcm_token')->nullable();
                $table->integer('employee_earning_type')->default(1)->comment('1= monthly and 2 = daily wages');
                $table->integer('employee_deactivation_type')->nullable()->comment('1= resigned 2 = terminated');
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
        Schema::dropIfExists('tbl_employees');
    }
};
