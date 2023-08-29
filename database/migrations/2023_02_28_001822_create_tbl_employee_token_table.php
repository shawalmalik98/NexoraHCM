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
        if(!Schema::hasTable('tbl_employee_token')){
            Schema::create('tbl_employee_token', function (Blueprint $table) {
                $table->id('employee_token_id');
                $table->integer('employee_token_emp_id');
                $table->string('employee_token',50); // code-time-rand(4)-rand(4 alphanumeric)
                $table->string('employee_token_created_at');
                $table->string('employee_token_cancelled_at')->nullable();
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
        Schema::dropIfExists('tbl_employee_token');
    }
};
