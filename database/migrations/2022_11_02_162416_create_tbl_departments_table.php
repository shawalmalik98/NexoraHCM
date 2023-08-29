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
        if(!Schema::hasTable('tbl_departments')){
            Schema::create('tbl_departments', function (Blueprint $table) {
                $table->id('department_id');
                $table->string('department_name', 255);
                $table->string('department_create_time', 255);
                $table->string('department_update_time', 255)->nullable();
                $table->integer('department_hod')->nullable();
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
        Schema::dropIfExists('tbl_departments');
    }
};
