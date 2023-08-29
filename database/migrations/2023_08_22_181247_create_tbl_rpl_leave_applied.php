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
        if(!Schema::hasTable('tbl_rpl_leave_applied')){
            Schema::create('tbl_rpl_leave_applied', function (Blueprint $table) {
                $table->id('rpl_leave_id');
                $table->integer('rpl_leave_applied_by');
                $table->string('rpl_leave_applied_date', 25);
                $table->string('rpl_leave_worked_on_date', 25);
                $table->string('rpl_leave_required_for_date', 25);
                $table->string('rpl_leave_applied_reason', 1000);
                $table->integer('rpl_leave_approved_by_hr', )->comment('1 = Pending, 2 = Approved, 3 = Disapproved');
                $table->string('rpl_leave_approved_date_hr', 255)->nullable();
                $table->string('rpl_leave_created_at');

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
        Schema::dropIfExists('tbl_rpl_leave_applied');
    }
};
