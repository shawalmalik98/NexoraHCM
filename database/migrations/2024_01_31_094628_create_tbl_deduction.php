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
        if(!Schema::hasTable('tbl_deduction')){
            Schema::create('tbl_deduction', function (Blueprint $table) {
                $table->id('deduction_id');
                $table->integer('deduction_salary_id');
                $table->string('deduction_title', 1000)->nullable();
                $table->string('deduction_amount', 1000)->nullable();
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
        Schema::dropIfExists('tbl_deduction');
    }
};
