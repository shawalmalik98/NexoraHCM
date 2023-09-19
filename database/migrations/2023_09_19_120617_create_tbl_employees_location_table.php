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
        if(!Schema::hasTable('tbl_employees_location')){
            Schema::create('tbl_employees_location', function (Blueprint $table) {
                $table->id('employee_location_id');
                $table->integer('emp_loc_employee_id');
                $table->integer('emp_loc_loc_id');
                $table->string('created_at', 50);
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
        Schema::dropIfExists('tbl_employees_location');
    }
};
