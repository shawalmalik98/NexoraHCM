<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns read by EmployeeController::get_business_card_data() / update business card
 * that no earlier migration created.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('tbl_employees', 'employee_alt_name')) {
            Schema::table('tbl_employees', function (Blueprint $table) {
                $table->string('employee_alt_name', 191)->nullable();
            });
        }

        foreach (['office_phone' => 50, 'office_address' => 500, 'office_latlong' => 100, 'website' => 191] as $column => $length) {
            if (!Schema::hasColumn('tbl_company', $column)) {
                Schema::table('tbl_company', function (Blueprint $table) use ($column, $length) {
                    $table->string($column, $length)->nullable();
                });
            }
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tbl_employees', 'employee_alt_name')) {
            Schema::table('tbl_employees', function (Blueprint $table) {
                $table->dropColumn('employee_alt_name');
            });
        }

        foreach (['office_phone', 'office_address', 'office_latlong', 'website'] as $column) {
            if (Schema::hasColumn('tbl_company', $column)) {
                Schema::table('tbl_company', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
