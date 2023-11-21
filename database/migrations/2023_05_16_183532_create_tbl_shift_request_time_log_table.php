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
        if(!Schema::hasTable('tbl_shift_request_time_log')){
            Schema::create('tbl_shift_request_time_log', function (Blueprint $table) {
                $table->id('shift_log_id');
                $table->integer('shift_request_id');
                $table->integer('shift_employee_id');
                $table->integer('shift_log_type')->comment('1 = actual, 2 = updated');
                $table->date('shift_log_effect_date');
                $table->string('shift_log_start',100);
                $table->string('shift_log_end',100);
                $table->dateTime('shift_log_create_at');
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
        Schema::dropIfExists('tbl_shift_request_time_log');
    }
};
