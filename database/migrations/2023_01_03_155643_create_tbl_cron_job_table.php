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
        if(!Schema::hasTable('tbl_cron_job')){
            Schema::create('tbl_cron_job', function (Blueprint $table) {
                $table->id('cron_job_id');
                $table->string('function_name', 255);
                $table->date('schedule_time')->nullable();
                $table->datetime('execute_time');
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
        Schema::dropIfExists('tbl_cron_job');
    }
};
