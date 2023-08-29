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
        if(!Schema::hasTable('tbl_polling_options')){
            Schema::create('tbl_polling_options', function (Blueprint $table) {
                $table->id('polling_option_id');
                $table->integer('polling_id');
                $table->string('polling_options', 50);
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
        Schema::dropIfExists('tbl_polling_options');
    }
};
