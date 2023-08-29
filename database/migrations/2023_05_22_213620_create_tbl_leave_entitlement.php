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
      
        if(!Schema::hasTable('tbl_leave_entitlement')){
            Schema::create('tbl_leave_entitlement', function (Blueprint $table) {
                $table->id('leaves_id');
                $table->integer('leaves_employee_id');
                $table->integer('leave_type_id');
                $table->string('leave_allotement');
                $table->string('leave_ent_created_at',50);
                $table->string('leave_ent_updated_at',50)->nullable();
             
               
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
        Schema::dropIfExists('tbl_leave_entitlement');
    }
};
