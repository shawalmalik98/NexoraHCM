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
        if(!Schema::hasTable('tbl_advance_salary_request')){
            Schema::create('tbl_advance_salary_request', function (Blueprint $table) {
                $table->id('advance_salary_request_id');
                $table->integer('employee_id');
                $table->string('effective_payroll_period', 25);
                $table->double('advance_salary_amount', 8, 2);
                $table->longtext('request_message');
                $table->integer('request_approved_by_finance')->comment('1 = Pending, 2 = Approved, 3 = Disapproved');
                $table->string('request_approved_date_finance', 255)->nullable();
                $table->longtext('request_aprrover_message')->nullable();
                $table->string('request_status', 50)->comment('1 = Pending, 2 = Approved, 3 = Disapproved')->nullable();
                $table->string('request_created_at', 25);
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
        Schema::dropIfExists('tbl_advance_salary_request');
    }
};
