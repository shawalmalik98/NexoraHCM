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
        if(!Schema::hasTable('tbl_approval_flow_modules')){
            Schema::create('tbl_approval_flow_modules', function (Blueprint $table) {
                $table->id('appflow_module_id');
                $table->string('appflow_module_name', 255);
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
        Schema::dropIfExists('tbl_approval_flow_modules');
    }
};
