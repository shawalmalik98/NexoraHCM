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
        if(!Schema::hasTable('tbl_gadget_allotment')){
            Schema::create('tbl_gadget_allotment', function (Blueprint $table) {
                $table->id('allotment_id');
                $table->integer('allotment_employee_id');
                $table->integer('gadget_id');
                $table->string('allotment_created_date');
            
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
        //
        Schema::dropIfExists('tbl_gadget_allotment');
    }
};
