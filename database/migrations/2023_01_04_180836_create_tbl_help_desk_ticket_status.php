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
        if(!Schema::hasTable('tbl_help_desk_ticket_status')){
            Schema::create('tbl_help_desk_ticket_status', function (Blueprint $table) {
                $table->id('ticket_status_id');
                $table->integer('help_desk_id');
                $table->integer('ticket_status_changed_by');
                $table->integer('ticket_status')->default('1')->comment('1=TicketPending, 2=TicketOpen , 3=TicketRejected, 4=QueryResolvedFromDepartment,5=QueryResolvedFromEmployee');
                $table->string('ticket_status_created_time');
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
        Schema::dropIfExists('tbl_help_desk_ticket_status');
    }
};
