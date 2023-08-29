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
        if(!Schema::hasTable('tbl_leaves_applied')){
            Schema::create('tbl_leaves_applied', function (Blueprint $table) {
                $table->id('leave_applied_id');
                $table->string('leave_applied_by', 50);
                $table->string('leave_applied_date', 25);
                $table->string('leave_applied_for_date', 25);
                $table->string('leave_applied_reason', 1000);
                $table->string('leave_applied_type', 25);
                $table->string('leave_applied_attachment', 1000)->nullable();
                $table->integer('leave_approved_by_hr')->comment('1 = Pending, 2 = Approved, 3 = Disapproved');
                $table->string('leave_approved_date_hr', 255)->nullable();
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
        Schema::dropIfExists('tbl_leaves_applied');
    }
};
