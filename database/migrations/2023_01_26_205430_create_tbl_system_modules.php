<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('tbl_system_modules')){
            Schema::create('tbl_system_modules', function (Blueprint $table) {
                $table->id('module_id');
                $table->string('module_title',100);
                $table->string('module_url',255);
                $table->string('module_type',10)->comment('0 = basic, 1 = others');
                $table->integer('module_accessibility')->default(1)->comment('0 = restricted, 1 = allowed');
            });
        }
       
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_system_modules');
    }
};
