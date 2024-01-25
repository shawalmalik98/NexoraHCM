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
        if(!Schema::hasTable('tbl_login_history')){
            Schema::create('tbl_login_history', function (Blueprint $table) {
                $table->id('login_history_id');
                $table->integer('login_history_employee_id');
                $table->string('login_history_session_token', 1000)->nullable();
                $table->string('login_history_time', 1000);
                $table->string('login_history_ip', 1000);
                $table->string('login_history_country', 1000);
                $table->string('login_history_city', 1000);
                $table->string('login_history_latitude', 1000);
                $table->string('login_history_longitude', 1000);
                $table->integer('login_history_source')->default(1)->comment('1 = Web Application, 2 = Mobile Application, 3 = Proxy User');
                $table->integer('login_employee_device')->nullable();
                $table->integer('fingerprint')->nullable()->comment('0 = deactive, 1 = active');
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
        Schema::dropIfExists('tbl_login_history');
    }
};
