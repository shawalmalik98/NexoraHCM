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
        if(!Schema::hasTable('tbl_payroll_tax_slab')){
            Schema::create('tbl_payroll_tax_slab', function (Blueprint $table) {
                $table->id('tax_slab_id');
                $table->double('ts_lowest_yearly_income',10,2);
                $table->double('ts_highest_yearly_income',10,2);
                $table->double('ts_deduction_percentage',4,2)->default(0);
                $table->double('ts_deduction_amount',10,2)->default(0);
                $table->date('ts_fiscal_start'); //july 2022
                $table->date('ts_fiscal_end');    //june 2023

                $table->integer('ts_created_by');
                $table->string('ts_created_at');
                $table->string('ts_updated_at')->nullable();
                
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
        Schema::dropIfExists('tbl_payroll_tax_slab');
    }
};
