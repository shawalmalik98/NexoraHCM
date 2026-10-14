<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Business card / QR feature columns used by EmployeeController
 * (emp_unique_code, emp_qr_value) that no earlier migration created.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('tbl_employees', 'emp_unique_code')) {
            Schema::table('tbl_employees', function (Blueprint $table) {
                $table->string('emp_unique_code', 64)->nullable()->index();
            });
        }

        if (!Schema::hasColumn('tbl_employees', 'emp_qr_value')) {
            Schema::table('tbl_employees', function (Blueprint $table) {
                $table->string('emp_qr_value', 255)->nullable();
            });
        }

        DB::table('tbl_employees')->whereNull('emp_unique_code')->orderBy('employee_id')->each(function ($employee) {
            DB::table('tbl_employees')->where('employee_id', $employee->employee_id)
                ->update(['emp_unique_code' => (string) Str::uuid()]);
        }, 100);
    }

    public function down()
    {
        foreach (['emp_unique_code', 'emp_qr_value'] as $column) {
            if (Schema::hasColumn('tbl_employees', $column)) {
                Schema::table('tbl_employees', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
