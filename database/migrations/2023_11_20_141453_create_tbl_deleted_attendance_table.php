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
        Schema::create('tbl_deleted_attendance', function (Blueprint $table) {
            $table->id('deleted_attendance_id');
            $table->integer('att_record_id');
            $table->string('att_date', 50);
            $table->integer('att_emp_id');
            $table->string('att_checkin_time', 50)->nullable();
            $table->string('att_checkout_time', 50)->nullable();
            $table->integer('att_deleted_by');
            $table->longText('att_deleted_reason');
            $table->string('att_deleted_at', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_deleted_attendance');
    }
};
