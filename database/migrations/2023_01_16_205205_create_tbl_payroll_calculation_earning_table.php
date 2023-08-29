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
        if(!Schema::hasTable('tbl_payroll_calculation_earning')){
            Schema::create('tbl_payroll_calculation_earning', function (Blueprint $table) {
                $table->id('pc_earning_id');
                $table->integer('pc_earning_pc_id');
                $table->string('pc_earning_title',50)->nullable();
                $table->double('pc_earning_amount',8, 2);
                $table->integer('pc_earning_type')->default(1)->comment('1= monthly and 2 = daily wages');
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
        Schema::dropIfExists('tbl_payroll_calculation_earning');
    }
};
