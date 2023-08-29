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
        if(!Schema::hasTable('tbl_employee_expense')){
            Schema::create('tbl_employee_expense', function (Blueprint $table) {
                
                $table->id('emp_expense_id');

                $table->integer('emp_expense_employee');
                $table->date('emp_expense_date');
                $table->integer('emp_expense_type')->comment('define in employee expense controller');
                $table->longText('emp_expense_description');
                $table->string('emp_expense_attachment');

                $table->integer('emp_expense_approved_by_hr')->comment('1 = Pending, 2 = Approved, 3 = Disapproved');
                $table->integer('emp_expense_approved_by_hr_id')->nullable();
                $table->dateTime('emp_expense_approved_by_hr_time')->nullable();
                $table->string('emp_expense_approved_by_hr_reason')->nullable();

                $table->integer('emp_expense_reimbursement')->comment('1 = Unpaid, 2 = Paid');
                $table->integer('emp_expense_payslip_id')->nullable();

                $table->string('emp_expense_created_at');
                $table->string('emp_expense_updated_at')->nullable();

            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tbl_employee_expense');
    }
};
