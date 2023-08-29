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

        if(!Schema::hasTable('tbl_requisition_status')){
            Schema::create('tbl_requisition_status', function (Blueprint $table) {
                $table->id('requisition_status_id');
                $table->integer('main_requisition_id');
                $table->string('requisition_status', 255);
                $table->integer('requisition_status_created_by');
                $table->string('requisition_status_created_at', 255);

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
        Schema::dropIfExists('tbl_requisition_status');
    }
};
