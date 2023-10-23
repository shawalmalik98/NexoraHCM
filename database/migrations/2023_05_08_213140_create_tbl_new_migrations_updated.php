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

        if(Schema::hasTable('tbl_employees')){

            if (!Schema::hasColumn('tbl_employees', 'employee_web_access')) {
                Schema::table('tbl_employees', function (Blueprint $table) {
                    $table->integer('employee_web_access')->nullable()->comment('0 = deactive  1 = active');
                });
            }

            if (!Schema::hasColumn('tbl_employees', 'employee_mobile_access')) {
                Schema::table('tbl_employees', function (Blueprint $table) {
                    $table->integer('employee_mobile_access')->nullable()->comment('0 = deactive 1 = active');
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
