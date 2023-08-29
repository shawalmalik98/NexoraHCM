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
        if(!Schema::hasTable('tbl_error_logs')){
            Schema::create('tbl_error_logs', function (Blueprint $table) {
                $table->id('error_log_id');
                $table->string('error_log_module', 50);
                $table->string('error_log_action_url', 255);
                $table->string('error_log_current_url', 255);
                $table->longtext('error_log_message');
                $table->integer('error_log_source')->default(1)->comment('1 = Web App, 2 = Mobile App');
                $table->integer('error_log_generated_employee');
                $table->string('error_log_schedule_time',100)->nullable();
            
                
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
        Schema::dropIfExists('tbl_error_logs');
    }
};
