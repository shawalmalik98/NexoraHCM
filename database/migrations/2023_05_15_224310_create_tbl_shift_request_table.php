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
        if(!Schema::hasTable('tbl_shift_request')){
            Schema::create('tbl_shift_request', function (Blueprint $table) {
                $table->id('shift_request_id');
                $table->integer('request_employee_id');
                $table->string('request_shift_start',100);
                $table->string('request_shift_end',100);
                $table->date('request_shift_effect_date');
                $table->string('request_shift_reason',1000);
                $table->integer('request_status')->comment('1 = Pending, 2 = Approved, 3 = Disapproved');
                $table->integer('request_approved_by_hr')->nullable();
                $table->dateTime('request_submit_time');
                $table->dateTime('request_approved_time')->nullable();

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
        Schema::dropIfExists('tbl_shift_request');
    }
};
