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
        if(!Schema::hasTable('tbl_employee_expense_amount')){
            Schema::create('tbl_employee_expense_amount', function (Blueprint $table) {
                $table->id('emp_expense_amt_id');
                $table->integer('emp_expense_exp_id');
                $table->integer('emp_expense_amt_emp_id');
                $table->double('emp_expense_amount',10,2);
                $table->dateTime('emp_expense_create_at');
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
        Schema::dropIfExists('tbl_employee_expense_amount');
    }
};
