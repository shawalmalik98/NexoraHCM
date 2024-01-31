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

        
        // if(Schema::hasTable('tbl_alerts')){

        //     if (!Schema::hasColumn('tbl_alerts', 'location')) {
        //         Schema::table('tbl_alerts', function (Blueprint $table) {
        //             $table->string('location', 1000)->nullable();
        //         });
        //     }
        // }    

        // if(Schema::hasTable('tbl_login_history')){

        //     if (!Schema::hasColumn('tbl_login_history', 'fingerprint')) {
        //         Schema::table('tbl_login_history', function (Blueprint $table) {
        //             $table->integer('fingerprint')->nullable()->comment('0 = deactive, 1 = active');
        //         });
        //     }
        // }    
       
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
