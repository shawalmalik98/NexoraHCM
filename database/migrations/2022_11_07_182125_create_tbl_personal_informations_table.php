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
        if(!Schema::hasTable('tbl_personal_informations')){
            Schema::create('tbl_personal_informations', function (Blueprint $table) {
                $table->id('information_id');
                $table->integer('employeeid');
                $table->string('fathername', 50)->nullable();
                $table->string('dob', 10)->nullable();
                $table->string('pob', 255)->nullable();
                $table->string('gender', 10)->nullable();
                $table->string('bloodgroup', 10)->nullable();
                $table->string('dateofexpiry', 10)->nullable();
                $table->string('birthplace', 50)->nullable();
                $table->string('citizenship', 50)->nullable();
                $table->string('passport', 50)->nullable();
                $table->string('religion', 50)->nullable();
                $table->string('maritalstatus', 20)->nullable();
                $table->string('dateofanniversary', 10)->nullable();
                $table->string('permanentaddress', 255)->nullable();
                $table->string('currentaddress', 255)->nullable();
                $table->string('bankname', 100)->nullable();
                $table->string('bankcode', 100)->nullable();
                $table->string('accountno', 100)->nullable();
                $table->string('accounttitle', 100)->nullable();
                $table->string('ibannumber', 100)->nullable();
                $table->longtext('employementhistory')->nullable();
                $table->longtext('academiccredential')->nullable();
                $table->longtext('professionalqualification')->nullable();
                $table->string('referencename1', 50)->nullable();
                $table->string('referencedesignation1', 50)->nullable();
                $table->string('referencecontact1', 50)->nullable();
                $table->string('referencecompany1', 200)->nullable();
                $table->string('referencecompanyaddress1', 200)->nullable();
                $table->string('referenceofficialemailid1', 50)->nullable();
                $table->string('referencecapacity1', 50)->nullable();
                $table->string('referencename2', 50)->nullable();
                $table->string('referencedesignation2', 50)->nullable();
                $table->string('referencecontact2', 50)->nullable();
                $table->string('referencecompany2', 200)->nullable();
                $table->string('referencecompanyaddress2', 200)->nullable();
                $table->string('referenceofficialemailid2', 50)->nullable();
                $table->string('referencecapacity2', 50)->nullable();
                $table->string('relativename1', 50)->nullable();
                $table->string('relativedesignation1', 50)->nullable();
                $table->string('relativedepartment1', 50)->nullable();
                $table->string('relativerelationtype1', 50)->nullable();
                $table->string('relativename2', 50)->nullable();
                $table->string('relativedesignation2', 50)->nullable();
                $table->string('relativedepartment2', 50)->nullable();
                $table->string('relativerelationtype2', 50)->nullable();
                $table->string('appliedbefore', 25)->nullable();
                $table->string('whatposition', 25)->nullable();
                $table->string('resultofapplication', 25)->nullable();
                $table->string('profile_avatar', 1000)->nullable();
                $table->string('emergency_contact_no', 50)->nullable();
                $table->string('emergency_name', 100)->nullable();
                $table->string('employee_relation', 100)->nullable();
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
        Schema::dropIfExists('tbl_personal_informations');
    }
};
