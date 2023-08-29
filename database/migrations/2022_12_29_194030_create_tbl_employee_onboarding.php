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
        if(!Schema::hasTable('tbl_employee_onboarding')){
            Schema::create('tbl_employee_onboarding', function (Blueprint $table) {
                $table->id('onboarding_id');
                $table->integer('onboarding_candidate_id');
                $table->string('department_name', 255);
                $table->string('doj', 50);
                $table->integer('onboard_status')->default(0);
                $table->string('employee_hired_created_at', 50);
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
        Schema::dropIfExists('tbl_employee_onboarding');
    }
};
