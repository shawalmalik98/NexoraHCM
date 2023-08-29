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
        if(!Schema::hasTable('tbl_employee_salary')){
            Schema::create('tbl_employee_salary', function (Blueprint $table) {
                $table->id('salary_id');
                $table->integer('salary_employee_id');
                $table->integer('salary_amount');
                $table->date('salary_effected_date');
                $table->string('salary_created_date', 255);
                $table->integer('salary_earning_type')->default(1)->comment('1= monthly and 2 = daily wages');
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
        Schema::dropIfExists('tbl_employee_salary');
    }
};
