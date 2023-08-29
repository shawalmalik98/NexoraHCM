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
        if(!Schema::hasTable('tbl_email_configuration')){
            Schema::create('tbl_email_configuration', function (Blueprint $table) {
                $table->id('email_config_id');
                $table->string('email_config_mailer');
                $table->string('email_config_host');
                $table->string('email_config_email');
                $table->string('email_config_pass');
                $table->string('email_config_port');
                $table->string('email_config_encryption');
                $table->string('email_config_from_address');
                $table->string('email_config_from_name');
                $table->string('email_config_created_at');
                $table->string('email_config_updated_at')->nullable();
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
        Schema::dropIfExists('tbl_email_configuration');
    }
};