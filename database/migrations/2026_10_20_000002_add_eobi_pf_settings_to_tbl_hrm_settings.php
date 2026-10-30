<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Statutory payroll deductions:
 *  EOBI - employee share = eobi_employee_percentage % of eobi_minimum_wage per payslip
 *  PF   - employee share = pf_employee_percentage % of basic salary per payslip
 */
return new class extends Migration
{
    private $columns = ['eobi_apply', 'eobi_minimum_wage', 'eobi_employee_percentage', 'pf_apply', 'pf_employee_percentage'];

    public function up()
    {
        Schema::table('tbl_hrm_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_hrm_settings', 'eobi_apply')) {
                $table->integer('eobi_apply')->default(0);
            }
            if (!Schema::hasColumn('tbl_hrm_settings', 'eobi_minimum_wage')) {
                $table->decimal('eobi_minimum_wage', 12, 2)->default(37000);
            }
            if (!Schema::hasColumn('tbl_hrm_settings', 'eobi_employee_percentage')) {
                $table->decimal('eobi_employee_percentage', 5, 2)->default(1);
            }
            if (!Schema::hasColumn('tbl_hrm_settings', 'pf_apply')) {
                $table->integer('pf_apply')->default(0);
            }
            if (!Schema::hasColumn('tbl_hrm_settings', 'pf_employee_percentage')) {
                $table->decimal('pf_employee_percentage', 5, 2)->default(8.33);
            }
        });
    }

    public function down()
    {
        foreach ($this->columns as $column) {
            if (Schema::hasColumn('tbl_hrm_settings', $column)) {
                Schema::table('tbl_hrm_settings', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
