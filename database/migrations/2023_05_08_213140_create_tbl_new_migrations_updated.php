<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   

        if(Schema::hasTable('tbl_attendance')){

            if (!Schema::hasColumn('tbl_attendance', 'sap_ci_flag')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->integer('sap_ci_flag')->nullable();
                });
            }

            if (!Schema::hasColumn('tbl_attendance', 'sap_co_flag')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->integer('sap_co_flag')->nullable();
                });
            }

            if (!Schema::hasColumn('tbl_attendance', 'sap_ci_timestamp')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->datetime('sap_ci_timestamp')->nullable();
                });
            }

            if (!Schema::hasColumn('tbl_attendance', 'sap_co_timestamp')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->datetime('sap_co_timestamp')->nullable();
                });
            }

        }       
       
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::dropIfExists('tbl_new_migrations_updated');
    }
};
