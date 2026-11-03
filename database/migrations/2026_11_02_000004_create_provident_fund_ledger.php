<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Provident fund: every payroll adds the employee share (deducted from salary) and the same company share
 * to the employee's fund; profit / withdrawals go in the same ledger. The payslip shows the balance.
 * Payroll rows also keep the income tax details (yearly taxable income, slab %, yearly tax) for the payslip.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tbl_provident_fund')) {
            Schema::create('tbl_provident_fund', function (Blueprint $table) {
                $table->id('pf_id');
                $table->unsignedBigInteger('pf_employee_id')->index();
                $table->unsignedBigInteger('pf_payroll_id')->nullable()->index();
                // contribution, profit, permanent_withdrawal, temporary_withdrawal, withdrawal_recovery
                $table->string('pf_type', 30)->default('contribution');
                $table->decimal('pf_member_amount', 14, 2)->default(0);
                $table->decimal('pf_company_amount', 14, 2)->default(0);
                $table->date('pf_date');
                $table->string('pf_remarks', 255)->nullable();
                $table->unsignedBigInteger('pf_created_by')->nullable();
                $table->dateTime('pf_created_at')->nullable();
            });
        }

        if (Schema::hasTable('tbl_hrm_settings') && !Schema::hasColumn('tbl_hrm_settings', 'pf_company_percentage')) {
            Schema::table('tbl_hrm_settings', function (Blueprint $table) {
                $table->decimal('pf_company_percentage', 5, 2)->nullable()->default(8.33)->after('pf_employee_percentage');
            });
        }

        if (Schema::hasTable('tbl_payroll_calculation') && !Schema::hasColumn('tbl_payroll_calculation', 'pc_tax_taxable_income')) {
            Schema::table('tbl_payroll_calculation', function (Blueprint $table) {
                $table->decimal('pc_tax_taxable_income', 14, 2)->nullable();
                $table->decimal('pc_tax_slab_percentage', 6, 2)->nullable();
                $table->decimal('pc_tax_yearly', 14, 2)->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tbl_provident_fund');
        if (Schema::hasColumn('tbl_hrm_settings', 'pf_company_percentage')) {
            Schema::table('tbl_hrm_settings', function (Blueprint $table) {
                $table->dropColumn('pf_company_percentage');
            });
        }
        if (Schema::hasColumn('tbl_payroll_calculation', 'pc_tax_taxable_income')) {
            Schema::table('tbl_payroll_calculation', function (Blueprint $table) {
                $table->dropColumn(['pc_tax_taxable_income', 'pc_tax_slab_percentage', 'pc_tax_yearly']);
            });
        }
    }
};
