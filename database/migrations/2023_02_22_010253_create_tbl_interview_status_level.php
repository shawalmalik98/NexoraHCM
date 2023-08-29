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
        if(!Schema::hasTable('tbl_interview_status_level')){
            Schema::create('tbl_interview_status_level', function (Blueprint $table) {
                $table->id('interview_status_level_id');
                $table->string('interview_status_level', 255);

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
        Schema::dropIfExists('tbl_interview_status_level');
    }
};
