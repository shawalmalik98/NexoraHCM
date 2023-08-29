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
        if(!Schema::hasTable('tbl_employees_gadgets')){
            Schema::create('tbl_employees_gadgets', function (Blueprint $table) {
                $table->id('gadget_id');
                $table->string('gadget_type', 255);
                $table->string('gadget_brand', 255);
                $table->string('gadget_model_no', 255);
                $table->string('gadget_serial_no', 255);
                $table->string('gadget_configuration', 1000);
                $table->string('gadget_mouse', 255)->nullable();
                $table->string('gadget_keyboard', 255)->nullable();
                $table->string('gadget_lcd', 255)->nullable();
                $table->string('gadget_headphone', 255)->nullable();
                $table->string('gadget_mac_wifi', 255)->nullable();
                $table->string('gadget_mac_lan', 255)->nullable();
                $table->integer('gadget_status')->nullable();
                $table->string('gadget_create_date', 255);
                $table->string('gadget_update_date', 255)->nullable();
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
        Schema::dropIfExists('tbl_employees_gadgets');
    }
};
