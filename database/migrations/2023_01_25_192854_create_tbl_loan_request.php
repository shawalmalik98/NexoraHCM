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
        if(!Schema::hasTable('tbl_loan_request')){
            Schema::create('tbl_loan_request', function (Blueprint $table) {
                $table->id('loan_request_id');
                $table->integer('request_employee_id');
                $table->string('loan_reason', 50);
                $table->longtext('loan_reason_message');
                $table->double('loan_request_amount', 10, 2);
                $table->integer('loan_deduction_type')->comment('1 = fixed amount, 2 = percentage');
                $table->string('loan_deduction_start', 25);
                $table->double('loan_deduction_amount', 10, 2);
                $table->integer('request_approved_by_finance')->comment('1 = Pending, 2 = Approved, 3 = Disapproved');
                $table->string('request_approved_date_finance', 255)->nullable();
                $table->longtext('request_aprrover_message')->nullable();
                $table->string('loan_applied_date', 25);
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
        Schema::dropIfExists('tbl_loan_request');
    }
};
