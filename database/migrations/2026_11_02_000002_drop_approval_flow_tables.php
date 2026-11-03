<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The "Approval Flow" screen was removed: what it saved was never used by any approval.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('tbl_approval_flow_details');
        Schema::dropIfExists('tbl_approval_flow_modules');
    }

    public function down()
    {
        if (!Schema::hasTable('tbl_approval_flow_modules')) {
            Schema::create('tbl_approval_flow_modules', function (Blueprint $table) {
                $table->id('appflow_module_id');
                $table->string('appflow_module_name', 255);
            });
        }
        if (!Schema::hasTable('tbl_approval_flow_details')) {
            Schema::create('tbl_approval_flow_details', function (Blueprint $table) {
                $table->id('appflow_detail_id');
                $table->integer('appflow_module_id');
                $table->integer('appflow_seq_no');
                $table->integer('appflow_department');
            });
        }
    }
};
