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
        if(!Schema::hasTable('tbl_salary_division')){
            Schema::create('tbl_salary_division', function (Blueprint $table) {
                $table->id('salary_division_id');
                $table->string('salary_division_title', 255);
                $table->double('salary_division_percentage',5,2);
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
        Schema::dropIfExists('tbl_salary_division');
    }
};
