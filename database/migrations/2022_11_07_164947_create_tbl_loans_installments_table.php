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
        if(!Schema::hasTable('tbl_loan_installments')){
            Schema::create('tbl_loan_installments', function (Blueprint $table) {
                $table->id('loan_installment_id');
                $table->integer('loan_installment_loan_id',);
                $table->double('loan_installment_amount', 8, 2);
                $table->string('loan_installment_type', 255)->nullable();
                $table->string('loan_installment_date', 50);
                $table->integer('loan_installment_payroll_id')->nullable();
                $table->string('loan_installment_source', 50);
                $table->string('loan_installment_remarks', 255);
                $table->string('loan_installment_created_by',50);
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
        Schema::dropIfExists('tbl_loan_installments');
    }
};
