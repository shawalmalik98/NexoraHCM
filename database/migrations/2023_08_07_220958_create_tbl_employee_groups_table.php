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
        if(!Schema::hasTable('tbl_employee_groups')){
            Schema::create('tbl_employee_groups', function (Blueprint $table) {
                $table->id('employee_group_id');
                $table->string('group_name', 50);
                $table->integer('created_by');
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
        Schema::dropIfExists('tbl_employee_groups');
    }
};
