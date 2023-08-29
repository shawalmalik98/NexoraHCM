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
        if(!Schema::hasTable('tbl_attendance_deduction_rule')){
            Schema::create('tbl_attendance_deduction_rule', function (Blueprint $table) {
                $table->id('attendance_deduction_rule_id');
                $table->integer('attendance_deduction_rule_type')->comment('1= Late Coming/Early Out 2 = Half Day');
                $table->integer('attendance_deduction_rule_day_count');
                $table->integer('attendance_deduction_rule_deduction_count');
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
        Schema::dropIfExists('tbl_attendance_deduction_rule');
    }
};
