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
        if(!Schema::hasTable('tbl_employee_documents')){
            Schema::create('tbl_employee_documents', function (Blueprint $table) {
                $table->id('document_id');
                $table->integer('employee_id');
                $table->integer('document_uploaded_by')->nullable();
                $table->string('document_title', 255)->nullable();
                $table->string('document_name', 255);
                $table->string('document_upload_date', 50);
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
        Schema::dropIfExists('tbl_employee_documents');
    }
};
