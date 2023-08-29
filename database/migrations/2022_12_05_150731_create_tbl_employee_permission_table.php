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
        if(!Schema::hasTable('tbl_employee_permission')){
            Schema::create('tbl_employee_permission', function (Blueprint $table) {
                $table->id('permission_id');
                $table->string('module_url', 255);
                $table->integer('employee_id');
                $table->integer('permission_status')->comment('0 = Not Allowed , 1 = Yes Allowed');
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
        Schema::dropIfExists('tbl_employee_permission');
    }
};
