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
       
        if(!Schema::hasTable('tbl_help_desk_for_employees')){
            Schema::create('tbl_help_desk_for_employees', function (Blueprint $table) {
                $table->id('hdf_emp_id');
                $table->integer('hd_id');
                $table->integer('hdf_employee_id')->nullable();
                $table->string('created_time',255);
             
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
        Schema::dropIfExists('tbl_help_desk_for_employees');
    }
};
