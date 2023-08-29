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
        if(!Schema::hasTable('tbl_request_approval')){
            Schema::create('tbl_request_approval', function (Blueprint $table) {
                $table->id('request_approval_id');
                $table->integer('request_module_id');
                $table->integer('request_id');
                $table->integer('request_approver_id');
                $table->integer('request_approver_status');
                $table->longText('request_approver_msg')->nullable();
                $table->string('request_approval_time', 50);
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
        Schema::dropIfExists('tbl_request_approval');
    }
};
