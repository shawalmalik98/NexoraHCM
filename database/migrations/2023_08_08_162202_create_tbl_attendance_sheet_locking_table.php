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
        if(!Schema::hasTable('tbl_attendance_sheet_locking')){
            Schema::create('tbl_attendance_sheet_locking', function (Blueprint $table) {
                $table->id('sheet_lock_id');
                $table->string('from_date', 25);
                $table->string('to_date', 25);
                $table->integer('locked_by');
                $table->string('locked_date',25);
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
        Schema::dropIfExists('tbl_attendance_sheet_locking');
    }
};
