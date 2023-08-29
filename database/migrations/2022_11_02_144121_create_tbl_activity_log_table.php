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
        if(!Schema::hasTable('tbl_activity_log')){
            Schema::create('tbl_activity_log', function (Blueprint $table) {
                $table->id('activity_id');
                $table->string('activity_login_history_id', 50)->nullable();
                $table->string('activity_detail', 1000)->nullable();
                $table->string('activity_request_method', 255)->nullable();
                $table->string('activity_url', 1000)->nullable();
                $table->string('activity_employee_ip_address', 255)->nullable();
                $table->string('activity_created', 255)->nullable();
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
        Schema::dropIfExists('tbl_activity_log');
    }
};
