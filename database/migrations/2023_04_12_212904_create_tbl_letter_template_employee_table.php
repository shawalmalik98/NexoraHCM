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
        if(!Schema::hasTable('tbl_letter_template_employee')){
            Schema::create('tbl_letter_template_employee', function (Blueprint $table) {
                $table->id('temp_emp_id');
                $table->integer('temp_emp_create_by');
                $table->integer('temp_emp_letter_id');
                $table->longText('temp_emp_variables');
                $table->integer('temp_emp_employee_id')->nullable();
                $table->integer('temp_emp_submit')->nullable()->comment('1=unsubmit 2=submitted');
                $table->string('temp_emp_created_at');
                $table->string('temp_emp_updated_at')->nullable();
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
        Schema::dropIfExists('tbl_letter_template_employee');
    }
};
