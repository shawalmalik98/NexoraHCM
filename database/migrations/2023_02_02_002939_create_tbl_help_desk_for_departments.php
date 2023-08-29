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
      
        if(!Schema::hasTable('tbl_help_desk_for_departments')){
            Schema::create('tbl_help_desk_for_departments', function (Blueprint $table) {
                $table->id('hdf_dprt_id');
                $table->integer('hd_id');
                $table->integer('hdf_department_id')->nullable();
                $table->string('created_time',255);
             
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
        Schema::dropIfExists('tbl_help_desk_for_departments');
    }
};
