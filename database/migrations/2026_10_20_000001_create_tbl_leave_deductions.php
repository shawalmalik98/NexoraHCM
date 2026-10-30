<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Leaves taken from an employee's balance by payroll for late comings / half days
 * beyond the monthly allowance. leave_type_id NULL means no leave was left and the
 * day was deducted from salary instead. Rows are removed when their payroll is deleted.
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tbl_leave_deductions')) {
            return;
        }

        Schema::create('tbl_leave_deductions', function (Blueprint $table) {
            $table->bigIncrements('leave_deduction_id');
            $table->integer('ld_employee_id')->index();
            $table->integer('ld_leave_type_id')->nullable();
            $table->date('ld_date');
            $table->string('ld_reason', 50);
            $table->integer('ld_payroll_id')->index();
            $table->timestamp('ld_created_at')->useCurrent();

            $table->index(['ld_employee_id', 'ld_leave_type_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_leave_deductions');
    }
};
