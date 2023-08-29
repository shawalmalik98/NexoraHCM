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
        if(!Schema::hasTable('tbl_polling')){
            Schema::create('tbl_polling', function (Blueprint $table) {
                $table->id('polling_id');
                $table->string('polling_department', 255);
                $table->string('polling_title', 255);
                $table->string('polling_duration', 25);
                $table->integer('polling_days');
                $table->integer('polling_created_by');
                $table->integer('polling_status')->comment(' 1 = Active, 2 = Deactive');
                $table->string('created_at', 25);
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
        Schema::dropIfExists('tbl_polling');
    }
};
