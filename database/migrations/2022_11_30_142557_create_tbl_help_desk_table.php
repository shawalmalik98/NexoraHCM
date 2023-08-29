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
        if(!Schema::hasTable('tbl_help_desk')){
            Schema::create('tbl_help_desk', function (Blueprint $table) {
                $table->id('help_desk_id');
                $table->integer('help_desk_loged_by');
                $table->longText('help_message');
                $table->string('help_attachment')->nullable();
                $table->string('created_time',255);
    
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
        Schema::dropIfExists('tbl_help_desk');
    }
};
