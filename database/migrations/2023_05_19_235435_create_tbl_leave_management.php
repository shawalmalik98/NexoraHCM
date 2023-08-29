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
       
        if(!Schema::hasTable('tbl_leave_management')){
            Schema::create('tbl_leave_management', function (Blueprint $table) {
                $table->id('leave_manage_id');
                $table->string('leave_name',255);
                $table->integer('leave_visibilty')->default(1);
                $table->integer('leave_yearly_allotment')->nullable();
                $table->integer('leave_manag_created_by');
                $table->string('leave_manag_created_at',50);
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
        Schema::dropIfExists('tbl_leave_management');
    }
};
