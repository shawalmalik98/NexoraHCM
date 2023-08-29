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
        if(!Schema::hasTable('tbl_letter_template')){
            Schema::create('tbl_letter_template', function (Blueprint $table) {
                $table->id('letter_template_id');
                $table->integer('letter_template_create_by');
                $table->string('letter_template_name');
                $table->longText('letter_template_html');
                $table->longText('letter_template_variables');
                $table->string('letter_template_created_at');
                $table->string('letter_template_updated_at')->nullable();
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
        Schema::dropIfExists('tbl_letter_template');
    }
};
