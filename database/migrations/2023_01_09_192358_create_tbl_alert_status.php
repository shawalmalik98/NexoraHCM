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
        if(!Schema::hasTable('tbl_alert_status')){
            Schema::create('tbl_alert_status', function (Blueprint $table) {
                $table->id('alert_status_id');
                $table->integer('alert_status_alert_id');
                $table->integer('alert_status_employee_id');
                $table->string('created_at', 50);
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
        Schema::dropIfExists('tbl_alert_status');
    }
};
