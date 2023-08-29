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
        if(!Schema::hasTable('tbl_notifications')){
            Schema::create('tbl_notifications', function (Blueprint $table) {
                $table->id('notification_id');
                $table->string('notification_title', 1000);
                $table->string('notification_message', 1000);
                $table->string('notification_url', 1000);
                $table->string('notification_to_employee', 255)->nullable();
                $table->string('notification_to_manager1', 255)->nullable();
                $table->string('notification_to_manager2', 255)->nullable();
                $table->string('notification_from', 255);
                $table->string('notification_to_hr', 255);
                $table->string('notification_time', 255);
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
        Schema::dropIfExists('tbl_notifications');
    }
};
