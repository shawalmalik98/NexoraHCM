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

            if (!Schema::hasColumn('tbl_attendance', 'att_comp_date')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->string('att_comp_date', 50)->nullable();
                });
            }

        }

        if(Schema::hasTable('tbl_office_locations')){

            if (!Schema::hasColumn('tbl_office_locations', 'last_updated_by')) {
                Schema::table('tbl_office_locations', function (Blueprint $table) {
                    $table->integer('last_updated_by')->default(1);
                });
            }

        }

        if(Schema::hasTable('tbl_employees_location')){

            if (!Schema::hasColumn('tbl_employees_location', 'created_by')) {
                Schema::table('tbl_employees_location', function (Blueprint $table) {
                    $table->integer('created_by')->default(1);
                });
            }

        }

        if(Schema::hasTable('tbl_departments')){

            if (!Schema::hasColumn('tbl_departments', 'last_updated_by')) {
                Schema::table('tbl_departments', function (Blueprint $table) {
                    $table->integer('last_updated_by')->default(1);
                });
            }

        }

        if(Schema::hasTable('tbl_designations')){

            if (!Schema::hasColumn('tbl_designations', 'last_updated_by')) {
                Schema::table('tbl_designations', function (Blueprint $table) {
                    $table->integer('last_updated_by')->default(1);
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
