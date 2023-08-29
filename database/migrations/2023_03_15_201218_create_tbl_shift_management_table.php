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
        if(!Schema::hasTable('tbl_shift_management')){
            Schema::create('tbl_shift_management', function (Blueprint $table) {
                $table->id('shift_id');
                $table->integer('employee_id');
                $table->string('shift_start_from_date', 100);
                $table->string('shift_start_time', 100);
                $table->string('shift_end_time', 100);
                $table->integer('shift_created_by');
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
        Schema::dropIfExists('tbl_shift_management');
    }
};
