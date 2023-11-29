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

            if (!Schema::hasColumn('tbl_attendance', 'att_ci_marked_time')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->string('att_ci_marked_time', 50)->nullable();
                });
            }

            if (!Schema::hasColumn('tbl_attendance', 'att_co_marked_time')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->string('att_co_marked_time', 50)->nullable();
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
