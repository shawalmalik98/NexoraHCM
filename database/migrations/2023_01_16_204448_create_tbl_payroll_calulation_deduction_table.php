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
        if(!Schema::hasTable('tbl_payroll_calculation_deduction')){
            Schema::create('tbl_payroll_calculation_deduction', function (Blueprint $table) {
                $table->id('pc_deduction_id');
                $table->integer('pc_deduction_pc_id');
                $table->string('pc_deduction_title',50)->nullable();
                $table->double('pc_deduction_amount',10,2);
                $table->integer('pc_deduction_type')->default(1)->comment('1= monthly and 2 = daily wages');
                
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
        Schema::dropIfExists('tbl_payroll_calculation_deduction');
    }
};
