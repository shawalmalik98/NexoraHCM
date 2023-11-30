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
        if(!Schema::hasTable('tbl_alerts')){
            Schema::create('tbl_alerts', function (Blueprint $table) {
                $table->id('alert_id');
                $table->string('title', 255);
                $table->longtext('message');
                $table->string('department', 255);
                $table->string('location', 1000)->nullable();
                $table->string('scheduled_time', 255);
                $table->string('days', 255);
                $table->string('valid_dates', 255);
                $table->integer('created_by');
                $table->string('created_at', 50);
                $table->string('modified_at', 50)->nullable();
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
        Schema::dropIfExists('tbl_alerts');
    }
};
