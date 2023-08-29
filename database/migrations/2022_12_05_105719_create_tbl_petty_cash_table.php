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
        if(!Schema::hasTable('tbl_petty_cash')){
            Schema::create('tbl_petty_cash', function (Blueprint $table) {
                $table->id('petty_cash_id');
                $table->decimal('petty_cash_amount',10,2);
                $table->string('petty_cash_for_month_and_year', 25);
                $table->string('petty_cash_source', 50);
                $table->string('petty_cash_source_attachment', 255)->nullable();
                $table->integer('petty_cash_created_by')->default(1);
                $table->string('petty_cash_created_at', 25);
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
        Schema::dropIfExists('tbl_petty_cash');
    }
};
