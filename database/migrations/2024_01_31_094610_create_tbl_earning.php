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
        if(!Schema::hasTable('tbl_earning')){
            Schema::create('tbl_earning', function (Blueprint $table) {
                $table->id('earning_id');
                $table->integer('earning_salary_id');
                $table->string('earning_title', 1000)->nullable();
                $table->string('earning_amount', 1000)->nullable();
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
        Schema::dropIfExists('tbl_earning');
    }
};
