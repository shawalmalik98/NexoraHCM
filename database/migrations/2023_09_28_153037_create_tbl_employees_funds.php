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
        if(!Schema::hasTable('tbl_employees_funds')){
            Schema::create('tbl_employees_funds', function (Blueprint $table) {
                $table->id('ef_id');
                $table->integer('ef_employee_id');
                $table->integer('ef_fund_id');
                $table->string('ef_amount',255);
                $table->string('ef_created_at', 50);
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
        Schema::dropIfExists('tbl_employees_funds');
    }
};
