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
        if(!Schema::hasTable('tbl_requisition')){
            Schema::create('tbl_requisition', function (Blueprint $table) {
                $table->id('requisition_id');
                $table->string('requisition_department', 255);
                $table->string('requisition_job_title', 255);
                $table->string('requisition_job_type', 255);
                $table->integer('requisition_expected_salary')->nullable();
                $table->string('requisition_shift_start_time',50)->nullable();
                $table->string('requisition_shift_end_time',50)->nullable();
                $table->string('requisition_shift_time',50);
                $table->date('requisition_date_needed');
                $table->longtext('requisition_additional_info')->nullable();
                $table->integer('requisition_added_by');
                $table->integer('no_of_position');
                $table->integer('office_location');
                $table->string('create_at', 50);
                $table->string('update_at', 50)->nullable();
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
        Schema::dropIfExists('tbl_requisition');
    }
};
