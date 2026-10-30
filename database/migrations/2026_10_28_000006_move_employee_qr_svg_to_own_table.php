<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The business card QR code (an SVG of ~3.5 KB) lived inside every tbl_employees row, so any query that
 * reads employee rows read ~100 MB at 20k employees. It moves to its own table, read only by the business card.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tbl_employee_qr_codes')) {
            Schema::create('tbl_employee_qr_codes', function (Blueprint $table) {
                $table->unsignedBigInteger('employee_id')->primary();
                $table->longText('qr_svg');
            });
        }
        if (Schema::hasColumn('tbl_employees', 'emp_qr_svg')) {
            DB::statement('INSERT IGNORE INTO tbl_employee_qr_codes (employee_id, qr_svg) SELECT employee_id, emp_qr_svg FROM tbl_employees WHERE emp_qr_svg IS NOT NULL');
            Schema::table('tbl_employees', function (Blueprint $table) {
                $table->dropColumn('emp_qr_svg');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasColumn('tbl_employees', 'emp_qr_svg')) {
            Schema::table('tbl_employees', function (Blueprint $table) {
                $table->longText('emp_qr_svg')->nullable();
            });
        }
        if (Schema::hasTable('tbl_employee_qr_codes')) {
            DB::statement('UPDATE tbl_employees e JOIN tbl_employee_qr_codes q ON q.employee_id = e.employee_id SET e.emp_qr_svg = q.qr_svg');
            Schema::drop('tbl_employee_qr_codes');
        }
    }
};
