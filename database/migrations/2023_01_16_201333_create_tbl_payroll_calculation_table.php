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
        if(!Schema::hasTable('tbl_payroll_calculation')){
            Schema::create('tbl_payroll_calculation', function (Blueprint $table) {


                $table->id('pc_id');
                $table->integer('pc_employee_id');
                $table->integer('pc_month');
                $table->integer('pc_year');

                $table->date('pc_date_from');
                $table->date('pc_date_to');

                $table->integer('pc_created_by')->nullable();

                $table->string('pc_employee_designation',50);
                $table->string('pc_employee_department',50);
                $table->string('pc_employee_job_title',100);
                $table->double('pc_employee_net_salary',10, 2);
                $table->double('pc_employee_gross_salary',10, 2);

                $table->string('pc_employee_bank_name',50)->nullable();
                $table->string('pc_employee_bank_account',50)->nullable();
                $table->string('pc_employee_bank_iban',50)->nullable();


                $table->integer('pc_total_working_days')->default(0);
                $table->integer('pc_present')->default(0);
                $table->integer('pc_absent')->default(0);
                $table->integer('pc_off_day')->default(0);
                $table->integer('pc_leave')->default(0);
                $table->integer('pc_holiday')->default(0);
                

                $table->integer('pc_late_early')->default(0);
                $table->integer('pc_half_day')->default(0);
                

                $table->double('pc_absent_deduction',10, 2)->default(0);
                $table->double('pc_late_early_deduction',10, 2)->default(0);
                $table->double('pc_half_day_deduction',10, 2)->default(0);

                $table->integer('disburse')->default(1)->comment('1 = not disburse , 2 = disburse');
                $table->string('disburse_date')->nullable();
                $table->integer('disburse_by')->nullable();
            
                $table->string('pc_batch',20);
                $table->string('pc_created_at');
                $table->string('pc_updated_at')->nullable();
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
        Schema::dropIfExists('tbl_payroll_calculation');
    }
};
