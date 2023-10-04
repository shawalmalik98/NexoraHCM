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
        if(!Schema::hasTable('tbl_hrm_settings')){
            Schema::create('tbl_hrm_settings', function (Blueprint $table) {
                $table->id('setting_id');
                $table->string('company_logo', 1000);
                $table->string('company_name', 255);
                $table->string('company_email', 255);
                $table->integer('company_email_permission')->comment("0 = Can't apply email permissions. 1 = Apply email permissions.");
                $table->integer('push_notification_permission')->comment("0 = Can't apply push notification permissions. 1 = Apply  push notification permissions.");
                $table->string('company_favicon', 1000);
                $table->longText('company_address');
                $table->integer('check_in_flexibility_time')->comment('Time duration that should be work as an attendance checkin grace time.');
                $table->integer('check_out_flexibility_time')->comment('Time duration that should be work as an attendance checkout grace time.');
                $table->integer('can_apply_for_leaves')->default(0)->comment("0 = Can't apply for leave if leaves not remains. 1 = Can apply for leave if leaves not remains.");
                $table->integer('payroll_start_date')->nullable();
                $table->string('fiscal_date',100)->nullable();
                $table->integer('club_rule_apply')->nullable();
                $table->integer('tax_deduction_apply')->nullable();
                $table->longText('linked_post1')->nullable();
                $table->longText('linked_post2')->nullable();
                $table->string('name_on_payslip',100)->nullable();
                $table->string('department_on_payslip',100)->nullable();
                $table->string('signature_on_payslip')->nullable();
                $table->string('onboarding_email_name')->nullable();
                $table->string('no_of_employees', 255)->comment('unlimited for unlimited employees.');
                $table->integer('web_attendance')->default(0)->comment("0 = Can't apply web attendance permissions. 1 = Apply web attendance permissions.");
                $table->string('mobile_attendance')->default(0)->comment("0 = Can't apply mobile attendance permissions. 1 = Apply mobile attendance permissions.");
                $table->double('daily_wages_late_deduction',3,2)->nullable()->comment('Daily wages rate of deduction on late');
                $table->double('daily_wages_half_deduction',3,2)->nullable()->comment('Daily wages rate of deduction on half day');  
                $table->integer('annual_leave_display')->default(1);
                $table->integer('total_leave_all_leaves_visibility')->default(1);
                $table->string('system_currency',100)->default('PKR');
                $table->float('gross_salary_ratio', 8 , 5)->default(62.89);
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
        Schema::dropIfExists('tbl_hrm_settings');
    }
};
