<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A payroll loan installment the salary could not (fully) cover: what was due that month and what is
 * carried to the next month's payroll. Existing rows: nothing carried.
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tbl_loan_installments') && !Schema::hasColumn('tbl_loan_installments', 'loan_installment_carried')) {
            Schema::table('tbl_loan_installments', function (Blueprint $table) {
                $table->double('loan_installment_due', 12, 2)->nullable()->after('loan_installment_amount');
                $table->double('loan_installment_carried', 12, 2)->default(0)->after('loan_installment_due');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tbl_loan_installments', 'loan_installment_carried')) {
            Schema::table('tbl_loan_installments', function (Blueprint $table) {
                $table->dropColumn(['loan_installment_due', 'loan_installment_carried']);
            });
        }
    }
};
