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
       
        if(Schema::hasTable('tbl_office_locations')){
            
            if (!Schema::hasColumn('tbl_office_locations', 'distributor_code')) {
                Schema::table('tbl_office_locations', function (Blueprint $table) {
                    $table->string('distributor_code', 255)->nullable();
                });
            }
            
        }

        if(Schema::hasTable('tbl_hrm_settings')){
            
            if (!Schema::hasColumn('tbl_hrm_settings', 'mobile_app_version')) {
                Schema::table('tbl_hrm_settings', function (Blueprint $table) {
                    $table->string('mobile_app_version', 50)->nullable();
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
