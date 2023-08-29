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
        if(!Schema::hasTable('tbl_payroll_attempts')){
            Schema::create('tbl_payroll_attempts', function (Blueprint $table) {
                $table->id('attempt_id');
                $table->integer('employee_id');
                $table->string('password_entered', 255)->nullable();
                $table->integer('attempt_status')->comment('1 = success , 2 = failed');
                $table->string('created_at', 25);
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
        Schema::dropIfExists('tbl_payroll_attempts');
    }
};
