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
        if(!Schema::hasTable('tbl_interviews')){
            Schema::create('tbl_interviews', function (Blueprint $table) {
                $table->id('interview_id');
                $table->integer('interview_candidate_id');
                $table->integer('requisition_reference');
                $table->string('interview_type', 25)->default('Physical')->comment('1=Physical 2=Online');
                $table->longText('interview_link')->nullable();
                $table->string('interview_date', 255);
                $table->string('interview_duration', 255)->nullable();
                $table->integer('interview_room_nbr')->nullable();
                $table->string('interview_time', 10);
                $table->integer('interview_interviewer1');
                $table->integer('interview_interviewer2')->nullable();
                $table->string('interview_round', 255);
                $table->string('interview_additional_remarks', 1000)->nullable();
                $table->string('interview_interviewer_remarks', 1000)->nullable();
                $table->string('interview_interviewer_remarks2', 255)->nullable();
                $table->string('interview_created_by', 20)->nullable();
                $table->string('interview_scheduled_time', 255);
                $table->integer('interview_department')->nullable();
                $table->string('interview_jobtitle', 255)->nullable();
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
        Schema::dropIfExists('tbl_interviews');
    }
};
