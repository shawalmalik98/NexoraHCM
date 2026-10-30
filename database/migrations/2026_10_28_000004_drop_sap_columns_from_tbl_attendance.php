<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The SAP attendance sync is gone; its flags on tbl_attendance are no longer read or written.
 */
return new class extends Migration
{
    private $columns = ['sap_ci_flag', 'sap_co_flag', 'sap_ci_timestamp', 'sap_co_timestamp'];

    public function up()
    {
        $existing = array_values(array_filter($this->columns, fn($column) => Schema::hasColumn('tbl_attendance', $column)));
        if (!empty($existing)) {
            Schema::table('tbl_attendance', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }

    public function down()
    {
        Schema::table('tbl_attendance', function (Blueprint $table) {
            $table->integer('sap_ci_flag')->nullable();
            $table->integer('sap_co_flag')->nullable();
            $table->datetime('sap_ci_timestamp')->nullable();
            $table->datetime('sap_co_timestamp')->nullable();
        });
    }
};
