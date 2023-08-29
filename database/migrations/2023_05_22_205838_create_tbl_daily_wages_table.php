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
        if(!Schema::hasTable('tbl_daily_wages')){
            Schema::create('tbl_daily_wages', function (Blueprint $table) {

                $table->id('dw_id');
                $table->integer('dw_employee_id');
                $table->date('dw_of_date');

                $table->integer('dw_created_by')->nullable();

                $table->string('dw_employee_designation',50);
                $table->string('dw_employee_department',50);
                $table->string('dw_employee_job_title',100);
                $table->double('dw_employee_net_daily_wages',10, 2);
                $table->double('dw_employee_gross_daily_wages',10, 2);

                $table->string('dw_employee_bank_name',50)->nullable();
                $table->string('dw_employee_bank_account',50)->nullable();
                $table->string('dw_employee_bank_iban',50)->nullable();

                $table->integer('dw_present')->default(0);
                $table->integer('dw_absent')->default(0);
                $table->integer('dw_off_day')->default(0);
                $table->integer('dw_leave')->default(0);
                $table->integer('dw_holiday')->default(0);

                $table->integer('dw_late_early')->default(0);
                $table->integer('dw_half_day')->default(0);
                
                $table->double('dw_absent_deduction',10, 2)->default(0);
                $table->double('dw_late_early_deduction',10, 2)->default(0);
                $table->double('dw_half_day_deduction',10, 2)->default(0);

                $table->integer('dw_disburse')->default(1)->comment('1 = not disburse , 2 = disburse');
                $table->string('dw_disburse_date')->nullable();
                $table->integer('dw_disburse_by')->nullable();
            
                $table->string('dw_batch',20);
                $table->string('dw_created_at');
                $table->string('dw_updated_at')->nullable();
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
        Schema::dropIfExists('tbl_daily_wages');
    }
};
