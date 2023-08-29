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
        if(!Schema::hasTable('tbl_meetings')){
            Schema::create('tbl_meetings', function (Blueprint $table) {
                $table->id('meeting_id');
                $table->string('meeting_type', 25);
                $table->string('meeting_room', 255)->nullable();
                $table->longText('meeting_link')->nullable();
                $table->string('meeting_title', 255);
                $table->longtext('meeting_description')->nullable();
                $table->string('meeting_time', 50);
                $table->integer('host_id');
                $table->integer('meeting_status')->comment('1 = Schedule, 2 = Completed, 3 = Canceled');
                $table->longText('meeting_conclusion')->nullable();
                $table->string('meeting_created_at', 50);
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
        Schema::dropIfExists('tbl_meetings');
    }
};
