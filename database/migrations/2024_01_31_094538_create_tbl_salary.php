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
        if(!Schema::hasTable('tbl_salary')){
            Schema::create('tbl_salary', function (Blueprint $table) {
                $table->id('salary_id');
                $table->integer('salary_employee_id');
                $table->integer('salary_employee_code');
                $table->string('basic_salary', 1000)->nullable();
                $table->string('house_rent', 1000)->nullable();
                $table->string('utilities', 1000)->nullable();
                $table->string('gross_adjustment', 1000)->nullable();
                $table->string('created_at', 25)->nullable();
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
        Schema::dropIfExists('tbl_salary');
    }
};
