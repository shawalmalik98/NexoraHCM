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
                $table->string('employee_code', 255);
                $table->string('employee_name', 255);
                $table->string('employee_contactno', 255);
                $table->string('employee_email', 255);
                $table->string('employee_work_email', 255)->nullable();
                $table->string('employee_cnic', 255)->nullable();
                $table->string('employee_gender', 10)->nullable();
                $table->string('employee_doj', 25);
                $table->string('employee_status', 255);
                $table->integer('employee_designation');
                $table->integer('employee_department');
                $table->integer('employee_group')->default(1);
                $table->string('employee_jobtitle', 255);
                $table->string('employee_offday1', 255)->nullable();
                $table->string('employee_shift_flexibilty', 5)->nullable();
                $table->string('employee_password', 1000);
                $table->integer('employee_ra');
                $table->integer('employee_ra2')->nullable();
                $table->integer('employee_can_ra');
                $table->integer('employee_isactive');
                $table->string('employee_deactivate_date', 50)->nullable();
                $table->string('employee_jobtype', 50);
                $table->integer('employee_location')->nullable();
                $table->integer('employee_grade')->default(1);
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
