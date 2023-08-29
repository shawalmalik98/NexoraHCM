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
        if(!Schema::hasTable('tbl_letters')){
            Schema::create('tbl_letters', function (Blueprint $table) {
                $table->id('letter_id');
                $table->integer('letter_employee_id');
                $table->string('letter_title', 255);
                $table->string('document_name', 1000);
                $table->integer('uploader');
                $table->string('upload_date', 50);
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
        Schema::dropIfExists('tbl_letters');
    }
};
