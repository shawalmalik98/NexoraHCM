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
    
        if(!Schema::hasTable('tbl_over_time')){
            Schema::create('tbl_over_time', function (Blueprint $table) {
                $table->id('over_time_id');
                $table->integer('ovt_emp_id');
                $table->integer('ovt_payroll_id')->nullable();
                $table->string('extra_hours', 50);
                $table->string('over_time_date', 50);
                $table->string('over_time_salary', 255);
                $table->string('amount_payable', 255);
                $table->integer('over_time_created_by');
                $table->integer('over_time_reibursement_status')->default(0);
                $table->string('over_time_created_at', 255);

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
        Schema::dropIfExists('tbl_over_time');
    }
};
