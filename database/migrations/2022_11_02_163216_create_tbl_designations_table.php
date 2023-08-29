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
        if(!Schema::hasTable('tbl_designations')){
            Schema::create('tbl_designations', function (Blueprint $table) {
                $table->id('designation_id');
                $table->string('designation_name', 255);
                $table->string('department_id', 255);
                $table->string('designation_create_time', 255)->nullable();
                $table->string('designation_update_time', 255)->nullable();
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
        Schema::dropIfExists('tbl_designations');
    }
};
