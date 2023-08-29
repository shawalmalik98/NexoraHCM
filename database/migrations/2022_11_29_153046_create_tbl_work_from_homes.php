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
        if(!Schema::hasTable('tbl_work_from_homes')){
            Schema::create('tbl_work_from_homes', function (Blueprint $table) {
                $table->id('wfh_request_id');
                $table->string('wfh_employee_id', 100);
                $table->string('wfh_request_date', 255);
                $table->string('wfh_request_message', 1000);
                $table->integer('wfh_request_approved_by_hr')->comment('1 = Pending, 2 = Approved, 3 = Disapproved');
                $table->string('wfh_request_approved_date_hr', 255)->nullable();
                $table->string('wfh_request_submition_date', 100);
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
        Schema::dropIfExists('tbl_work_from_homes');
    }
};
