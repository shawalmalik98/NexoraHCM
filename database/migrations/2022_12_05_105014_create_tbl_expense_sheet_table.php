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
        if(!Schema::hasTable('tbl_expense_sheet')){
            Schema::create('tbl_expense_sheet', function (Blueprint $table) {
                $table->id('expense_id');
                $table->string('expense_date', 25);
                $table->string('expense_reason', 255);
                $table->string('expense_source', 255);
                $table->string('expense_attachment', 1000)->nullable();
                $table->decimal('expense_amount',10,2);
                $table->integer('expense_created_by')->default(1);
                $table->integer('expense_locked')->nullable();
                $table->integer('expense_locked_created_by')->nullable();
                $table->string('expense_created_at', 25);
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
        Schema::dropIfExists('tbl_expense_sheet');
    }
};
