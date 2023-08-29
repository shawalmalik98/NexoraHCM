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
        if(!Schema::hasTable('tbl_loans')){
            Schema::create('tbl_loans', function (Blueprint $table) {
                $table->id('loan_id');
                $table->integer('loan_request_id')->nullable();
                $table->string('employee_code', 255);
                $table->string('loan_given_amount', 255);
                $table->string('loan_submitted_amount', 255)->nullable();
                $table->string('loan_remaining_amount', 255)->nullable();
                $table->string('loan_type', 255);
                $table->string('loan_other_reason', 255)->nullable();
                $table->string('loan_issuance_date', 255);
                $table->integer('loan_deduction_type')->comment('1 = fixed amount, 2 = percentage')->nullable();
                $table->double('loan_deduction_amount', 8, 2)->nullable();
                $table->string('loan_deduction_start', 25)->nullable();
                $table->string('loan_status', 255)->comment('1 = unpaid, 2 = paid');
                $table->string('last_updated', 255);
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
        Schema::dropIfExists('tbl_loans');
    }
};
