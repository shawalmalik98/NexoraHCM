<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Daily wages removed from the product: every employee is paid monthly. The module, its permissions
 * and the wages table go; employee_earning_type / salary_earning_type stay (always 1 = monthly).
 */
return new class extends Migration
{
    public function up()
    {
        foreach (['tbl_system_modules', 'tbl_employee_permission', 'tbl_role_permissions'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'module_url')) {
                DB::table($table)->where('module_url', '/daily-wages-payroll')->delete();
            }
        }
        Schema::dropIfExists('tbl_daily_wages');
    }

    public function down()
    {
        // not restored: the daily wages screens and code were removed
    }
};
