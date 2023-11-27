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
        if(!Schema::hasTable('tbl_mobile_access')){
            Schema::create('tbl_mobile_access', function (Blueprint $table) {
                $table->id('mobile_access_id');
                $table->integer('mobile_employee_id');
                $table->integer('access_button_id');
                $table->integer('access_buttons')->comment('1 = yes, 0 = no');
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
        Schema::dropIfExists('tbl_mobile_access');
    }
};
