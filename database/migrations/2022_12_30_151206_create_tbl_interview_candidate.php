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
        if(!Schema::hasTable('tbl_interview_candidate')){
            Schema::create('tbl_interview_candidate', function (Blueprint $table) {
                $table->id('candidate_id');
                $table->string('candidate_name');
                $table->string('interview_noticepriod', 255)->nullable();
                $table->string('interview_candidate_cv', 1000)->nullable();
                $table->string('interview_current_salary', 50)->nullable();
                $table->string('interview_expected_salary', 50)->nullable();
                $table->string('candidate_contact_no', 50)->nullable();
                $table->string('candidate_email', 150)->nullable();
                $table->string('candidate_created_at', 50);
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
        Schema::dropIfExists('tbl_interview_candidate');
    }
};
