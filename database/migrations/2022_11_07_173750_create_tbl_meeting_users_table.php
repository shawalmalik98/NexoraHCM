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
        if(!Schema::hasTable('tbl_meeting_users')){
            Schema::create('tbl_meeting_users', function (Blueprint $table) {
                $table->id('meet_user_id');
                $table->integer('users_meeting_id');
                $table->integer('user_id');
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
        Schema::dropIfExists('tbl_meeting_users');
    }
};
