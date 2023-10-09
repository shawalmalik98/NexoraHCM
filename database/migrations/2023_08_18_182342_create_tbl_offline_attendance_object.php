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
        if(!Schema::hasTable('tbl_offline_attendance_object')){
            Schema::create('tbl_offline_attendance_object', function (Blueprint $table) {
                $table->id('att_offline_id');
                $table->longText('att_offline_object');
                $table->string('att_offline_emp_id',50)->nullable();
                $table->string('att_created_at',50)->nullable();
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
        Schema::dropIfExists('tbl_offline_attendance_object');
    }
};
