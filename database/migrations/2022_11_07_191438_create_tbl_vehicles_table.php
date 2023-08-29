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
        if(!Schema::hasTable('tbl_vehicles')){
            Schema::create('tbl_vehicles', function (Blueprint $table) {
                $table->id('vehicle_id');
                $table->string('vehicle_type', 255);
                $table->string('vehicle_company_name', 255);
                $table->string('vehicle_name', 255);
                $table->string('vehicle_number', 255);
                $table->string('vehicle_model', 255);
                $table->string('vehicle_color', 255);
                $table->string('vehicle_engine_number', 255);
                $table->string('vehicle_chassis_number', 255);
                $table->string('vehicle_tax_paid_till', 255)->nullable();
                $table->string('vehicle_amount', 255)->nullable();
                $table->string('vehicle_ownership', 255)->nullable();
                $table->string('vehicle_installment', 255)->nullable();
                $table->string('vehicle_installment_date', 255)->nullable();
                $table->string('vehicle_status', 255)->comment('1 = active, 2 = deactive, 3 = inused, 4 = noinused');
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
        Schema::dropIfExists('tbl_vehicles');
    }
};
