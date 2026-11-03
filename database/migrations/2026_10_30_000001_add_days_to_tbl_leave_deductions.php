<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * How much leave a payroll deduction takes: 1 for an absent day, 0.5 for a late coming / half day beyond the
 * monthly allowance (two of them make one leave). Existing rows keep 1.
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tbl_leave_deductions') && !Schema::hasColumn('tbl_leave_deductions', 'ld_days')) {
            Schema::table('tbl_leave_deductions', function (Blueprint $table) {
                $table->decimal('ld_days', 5, 2)->default(1)->after('ld_reason');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tbl_leave_deductions', 'ld_days')) {
            Schema::table('tbl_leave_deductions', function (Blueprint $table) {
                $table->dropColumn('ld_days');
            });
        }
    }
};
