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
        if(!Schema::hasTable('tbl_employement_status')){
            Schema::create('tbl_employement_status', function (Blueprint $table) {
                $table->id('employement_status_id');
                $table->string('employement_status_name');
                $table->integer('employement_status_created_by');
                $table->string('employement_status_created_at',50);
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
        Schema::dropIfExists('tbl_employement_status');
    }
};
