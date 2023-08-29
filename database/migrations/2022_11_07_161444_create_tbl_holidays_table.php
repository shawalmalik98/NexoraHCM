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
        if(!Schema::hasTable('tbl_holidays')){
            Schema::create('tbl_holidays', function (Blueprint $table) {
                $table->id('holiday_id');
                $table->string('holiday_date', 10);
                $table->string('holiday_name', 50);
                $table->string('department_name', 255);
                $table->integer('is_overtime_applicable')->default(0)->comment('0 = not applicable, 1 = applicable');
                $table->integer('overtime_payment_type')->nullable()->comment('0 = employee salary, 1 = custom amount');
                $table->double('holiday_overtime_amount', 8, 2)->nullable();
                $table->integer('holiday_created_by')->default(1);
                $table->string('holiday_created_at', 50);
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
        Schema::dropIfExists('tbl_holidays');
    }
};
