<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tbl_employees_location') && !Schema::hasColumn('tbl_employees_location', 'created_by')) {
            Schema::table('tbl_employees_location', function (Blueprint $table) {
                $table->integer('created_by')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tbl_employees_location', 'created_by')) {
            Schema::table('tbl_employees_location', function (Blueprint $table) {
                $table->dropColumn('created_by');
            });
        }
    }
};
