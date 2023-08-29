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
        if(!Schema::hasTable('tbl_resignation_letters')){
            Schema::create('tbl_resignation_letters', function (Blueprint $table) {
                $table->id('resignation_id');
                $table->integer('resignation_employee');
                $table->longText('resignation_description');
                $table->string('resignation_from_date',25);
                $table->string('resignation_to_date',25);
                $table->string('request_approved_date_hr', 100)->nullable();
                $table->longText('request_aprroved_message')->nullable();
                $table->integer('resignation_status')->comment('1 = Pending, 2 = Approved, 3 = Disapproved');
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
        Schema::dropIfExists('tbl_resignation_letters');
    }
};
