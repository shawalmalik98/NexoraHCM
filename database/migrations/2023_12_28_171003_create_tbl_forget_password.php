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

        if(!Schema::hasTable('tbl_forget_password')){
            Schema::create('tbl_forget_password', function (Blueprint $table) {
                $table->id('forget_password_id');
                $table->integer('forget_employee_id');
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
        Schema::dropIfExists('tbl_forget_password');
    }
};
