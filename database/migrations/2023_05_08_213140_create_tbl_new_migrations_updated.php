<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        if(Schema::hasTable('tbl_hrm_settings')){
            
            if (!Schema::hasColumn('tbl_hrm_settings', 'mobile_attendance')) {
                Schema::table('tbl_hrm_settings', function (Blueprint $table) {
                    $table->string('mobile_attendance')->default(0);
                });
            }

            if (!Schema::hasColumn('tbl_hrm_settings', 'push_notification_permission')) {
                Schema::table('tbl_hrm_settings', function (Blueprint $table) {
                    $table->integer('push_notification_permission')->comment("0 = Can't apply push notification permissions. 1 = Apply  push notification permissions.");
                });
            }
            
        }
       
        if(Schema::hasTable('tbl_email_recipients')){
            if (!Schema::hasColumn('tbl_email_recipients', 'recipient_type')) {
                Schema::table('tbl_email_recipients', function (Blueprint $table) {
                    $table->string('recipient_type',100)->nullable();
                });
            }
        }

        if(Schema::hasTable('tbl_employees')){

            if (!Schema::hasColumn('tbl_employees', 'employee_deactivation_type')) {
                Schema::table('tbl_employees', function (Blueprint $table) {
                    $table->integer('employee_deactivation_type')->nullable()->comment('1= resigned 2 = terminated');
                });
            }

            if (!Schema::hasColumn('tbl_employees', 'employee_group')) {
                Schema::table('tbl_employees', function (Blueprint $table) {
                    $table->integer('employee_group')->default(1);
                });
            }

            if (!Schema::hasColumn('tbl_employees', 'employee_company')) {
                Schema::table('tbl_employees', function (Blueprint $table) {
                    $table->integer('employee_company')->default(1);
                });
            }

            Schema::table('tbl_employees', function (Blueprint $table) {
                $table->string('employee_offday1', 255)->nullable()->change();
            });
            
        }

        if(Schema::hasTable('tbl_hrm_settings')){
            if (!Schema::hasColumn('tbl_hrm_settings', 'system_currency')) {
                Schema::table('tbl_hrm_settings', function (Blueprint $table) {
                    $table->string('system_currency',100)->default('PKR');
                });
            }
            
        }
      
        if(Schema::hasTable('tbl_interview_candidate')){
            if (!Schema::hasColumn('tbl_interview_candidate', 'candidate_email')) {
                Schema::table('tbl_interview_candidate', function (Blueprint $table) {
                    $table->string('candidate_email', 150)->nullable();
                });
            }
            
        }

        if(Schema::hasTable('tbl_personal_informations')){
            if (!Schema::hasColumn('tbl_personal_informations', 'employee_relation')) {
                Schema::table('tbl_personal_informations', function (Blueprint $table) {
                    $table->string('employee_relation', 100)->nullable();
                });
            }
            
        }

        if(Schema::hasTable('tbl_attendance')){
            if (!Schema::hasColumn('tbl_attendance', 'att_remote_applicable')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->integer('att_remote_applicable')->default(0)->comment('0 = Attendence 1 = WFH');
                });
            }
            if (!Schema::hasColumn('tbl_attendance', 'att_marked_lat')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->string('att_marked_lat', 50)->nullable();
                });
            }
            if (!Schema::hasColumn('tbl_attendance', 'att_marked_long')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->string('att_marked_long', 50)->nullable();
                });
            }
            if (Schema::hasColumn('tbl_attendance', 'att_request_approval')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->integer('att_request_approval')->nullable()->comment("1 = Request Approval Attendance 2 = Request Approval Leave")->change();
                  
                });
            }

            if (Schema::hasColumn('tbl_attendance', 'att_check_in_source')) {
                Schema::table('tbl_attendance', function (Blueprint $table) {
                    $table->integer('att_check_in_source')->comment('1 = Web Application , 2 = Mobile Application, 3 = Update Attendance, 4 = Biometric Machine')->change();
                  
                  
                });
            }
        }
        if(Schema::hasTable('tbl_room_screen')){
            if (!Schema::hasColumn('tbl_room_screen', 'room_location_id')) {
                Schema::table('tbl_room_screen', function (Blueprint $table) {
                    $table->integer('room_location_id')->nullable();
                });
            }
            
        }


        if(Schema::hasTable('tbl_resignation_letters')){
            if (!Schema::hasColumn('tbl_resignation_letters', 'request_aprroved_message')) {
                Schema::table('tbl_resignation_letters', function (Blueprint $table) {
                    $table->longText('request_aprroved_message')->nullable();
                });
            }
        }

        if(Schema::hasTable('tbl_machine_attendance_logs')){
            if (!Schema::hasColumn('tbl_machine_attendance_logs', 'machine_id')) {
                Schema::table('tbl_machine_attendance_logs', function (Blueprint $table) {
                    $table->integer('machine_id')->nullable()->default(1);
                });
            }
        }

       
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::dropIfExists('tbl_new_migrations_updated');
    }
};
