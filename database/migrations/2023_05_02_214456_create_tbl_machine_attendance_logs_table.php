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
        if(!Schema::hasTable('tbl_machine_attendance_logs')){
            Schema::create('tbl_machine_attendance_logs', function (Blueprint $table) {
                $table->id('attendance_logs_id');
                $table->string('logs_status')->comment('1=success, 2=failed');
                $table->integer('machine_id')->nullable()->default(1);
                $table->string('logs_errors')->nullable();
                $table->string('logs_created_at');
                $table->string('logs_updated_at')->nullable();
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
        Schema::dropIfExists('tbl_machine_attendance_logs');
    }
};
