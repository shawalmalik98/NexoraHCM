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
        if(!Schema::hasTable('tbl_employement_period')){
            Schema::create('tbl_employement_period', function (Blueprint $table) {
                $table->id('employement_period_id');
                $table->integer('employement_status_id');
                $table->integer('employee_id');
                $table->string('effective_date',50);
                $table->integer('employement_period_created_by');
                $table->string('employement_period_created_at',50);
             
               
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
        Schema::dropIfExists('tbl_employement_period');
    }
};
