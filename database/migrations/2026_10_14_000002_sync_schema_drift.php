<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Brings the schema in line with columns/tables the code already uses
 * but that no earlier migration ever created.
 */
return new class extends Migration
{
    public function up()
    {
        foreach (['tbl_departments', 'tbl_designations', 'tbl_office_locations'] as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'last_updated_by')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->integer('last_updated_by')->nullable();
                });
            }
        }

        if (Schema::hasTable('tbl_employees') && !Schema::hasColumn('tbl_employees', 'emp_qr_svg')) {
            Schema::table('tbl_employees', function (Blueprint $table) {
                $table->longText('emp_qr_svg')->nullable();
            });
        }

        if (!Schema::hasTable('tbl_incentive')) {
            Schema::create('tbl_incentive', function (Blueprint $table) {
                $table->id('incentive_id');
                $table->string('incentive_period', 50);
                $table->string('employee_type', 150);
                $table->string('brand', 150)->nullable();
                $table->string('productive_criteria', 150)->nullable();
                $table->decimal('incentive', 14, 2)->default(0);
                $table->decimal('target_value', 14, 2)->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('sap_attendance_report')) {
            Schema::create('sap_attendance_report', function (Blueprint $table) {
                $table->id();
                $table->string('emp_code', 50)->index();
                $table->string('emp_name', 191)->nullable();
                $table->date('attendance_date')->nullable();
                $table->string('time_in', 20)->nullable();
                $table->string('time_out', 20)->nullable();
                $table->string('attendance_status', 50)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sap_attendance_report');
        Schema::dropIfExists('tbl_incentive');
        foreach (['tbl_departments', 'tbl_designations', 'tbl_office_locations'] as $tableName) {
            if (Schema::hasColumn($tableName, 'last_updated_by')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('last_updated_by');
                });
            }
        }
        if (Schema::hasColumn('tbl_employees', 'emp_qr_svg')) {
            Schema::table('tbl_employees', function (Blueprint $table) {
                $table->dropColumn('emp_qr_svg');
            });
        }
    }
};
