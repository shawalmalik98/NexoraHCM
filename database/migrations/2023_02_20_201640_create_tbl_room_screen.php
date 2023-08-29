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
        if(!Schema::hasTable('tbl_room_screen')){
            Schema::create('tbl_room_screen', function (Blueprint $table) {
                $table->id('room_id');
                $table->string('room_name', 255);
                $table->integer('room_location_id');
                $table->string('last_updated',255);
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
        Schema::dropIfExists('tbl_room_screen');
    }
};
