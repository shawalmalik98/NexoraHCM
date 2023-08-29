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
        if(!Schema::hasTable('tbl_email_recipients')){
            Schema::create('tbl_email_recipients', function (Blueprint $table) {
                $table->id('recipient_id');
                $table->integer('endpoint_id');
                $table->integer('receiever_id');
                $table->string('recipient_type',100)->nullable();
                $table->integer('recipients_created_by');
                $table->string('recipients_created_at',50);
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
        Schema::dropIfExists('tbl_email_recipients');
    }
};
