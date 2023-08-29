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
        if(!Schema::hasTable('tbl_mac_address')){
            Schema::create('tbl_mac_address', function (Blueprint $table) {
                $table->id('address_id');
                $table->string('mac_address', 255);
                $table->integer('mac_address_employee_id')->nullable();
                $table->string('mac_address_added_date', 255)->nullable();
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
        Schema::dropIfExists('tbl_mac_address');
    }
};
