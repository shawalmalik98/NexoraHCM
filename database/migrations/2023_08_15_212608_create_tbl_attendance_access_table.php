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
        if(!Schema::hasTable('tbl_attendance_access')){
            Schema::create('tbl_attendance_access', function (Blueprint $table) {
                $table->id('attendance_access_id');
                $table->integer('access_employee_id');
                $table->integer('attendance_access');
                $table->string('access_create_at', 25);
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
        Schema::dropIfExists('tbl_attendance_access');
    }
};
