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
        if(!Schema::hasTable('tbl_polling_status')){
            Schema::create('tbl_polling_status', function (Blueprint $table) {
                $table->id('polling_status_id');
                $table->integer('polling_option_id');
                $table->integer('polling_employee_id');
                $table->date('polling_time');
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
        Schema::dropIfExists('tbl_polling_status');
    }
};
