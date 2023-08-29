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
        if(!Schema::hasTable('tbl_employees_devices')){
            Schema::create('tbl_employees_devices', function (Blueprint $table) {
                $table->id('device_id');
                $table->integer('device_employee_id');
                $table->string('device_name', 1000);
                $table->string('device_token', 1000);
                $table->integer('device_status')->comment('1 = Active , 2 = Deactive');
                $table->string('device_created_at', 25);
                $table->string('device_updated_at', 25)->nullable();
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
        Schema::dropIfExists('tbl_employees_devices');
    }
};
