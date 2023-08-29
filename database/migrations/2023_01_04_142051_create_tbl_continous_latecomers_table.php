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
        if(!Schema::hasTable('tbl_continous_latecomers')){
            Schema::create('tbl_continous_latecomers', function (Blueprint $table) {
                $table->id('continous_latecomers_id');
                $table->integer('employee_id');
                $table->date('latecome_date1');
                $table->date('latecome_date2');
                $table->string('attendance_status');
                $table->date('create_date');
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
        Schema::dropIfExists('tbl_continous_latecomers');
    }
};
