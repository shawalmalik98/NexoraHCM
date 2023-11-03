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
        if(!Schema::hasTable('tbl_approval_flow_details')){
            Schema::create('tbl_approval_flow_details', function (Blueprint $table) {
                $table->id('appflow_detail_id');
                $table->integer('appflow_module_id');
                $table->integer('appflow_seq_no');
                $table->integer('appflow_department');
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
        Schema::dropIfExists('tbl_approval_flow_details');
    }
};
