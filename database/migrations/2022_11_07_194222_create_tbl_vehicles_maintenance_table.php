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
        if(!Schema::hasTable('tbl_vehicles_maintenance')){
            Schema::create('tbl_vehicles_maintenance', function (Blueprint $table) {
                $table->id('maintenance_id');
                $table->string('vehicle_id', 50);
                $table->string('maintenance_date', 50);
                $table->text('maintenance_item')->nullable();
                $table->string('maintenance_value', 255)->nullable();        
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
        Schema::dropIfExists('tbl_vehicles_maintenance');
    }
};
