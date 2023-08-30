<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $get_employees = DB::select('SELECT * FROM tbl_employees');
        $get_employees = json_decode(json_encode($get_employees), true);
        if(empty($get_employees)){
            DB::table('tbl_employees')->insert([
                'employee_code' => "001",
                'employee_name' => "Administrator Account",
                'employee_contactno' => "0648-8439872",
                'employee_email' => "saaahmed@ismailindustries.com",
                'employee_work_email' => "saaahmed@ismailindustries.com",
                'employee_cnic' => "44444-4444444-1",
                'employee_gender' => "Male",
                'employee_doj' => date('Y-m-d'),
                'employee_status' => 3,
                'employee_designation' => 1,
                'employee_department' => 1,
                'employee_company' => 1,
                'employee_group' => 1,
                'employee_jobtitle' => "Administrator",
                'employee_offday1' => "Sunday",
                'employee_shift_flexibilty' => "1",
                'employee_password' => Crypt::encryptString('123'),
                'employee_ra' => "1",
                'employee_ra2' => NULL,
                'employee_can_ra' => 1,
                'employee_isactive' => 1,
                'employee_deactivate_date' => NULL,
                'employee_jobtype' => "Office",
                'employee_earning_type' => 1,
                'employee_location' => 1,
            ]);
        }


        $get_personal_information = DB::select('SELECT * FROM tbl_personal_informations');
        if(empty($get_personal_information)){
            DB::table('tbl_personal_informations')->insert([
                'employeeid' => 1,
            ]);
        }

        $get_employee_salary = DB::select('SELECT * FROM tbl_employee_salary');
        if(empty($get_employee_salary)){
            DB::table('tbl_employee_salary')->insert([
                'salary_employee_id' => 1,
                'salary_amount' => 100000,
                'salary_effected_date' => date('Y-m-d'),
                'salary_created_date' => date('Y-m-d'),
            ]);
        }

        $get_employees = DB::select('SELECT * FROM tbl_employees');
        $get_employees = json_decode(json_encode($get_employees), true);
        $get_employement_period = DB::select('SELECT * FROM tbl_employement_period');
        if(count($get_employees) == 1){
            if(empty($get_employement_period)){
                DB::table('tbl_employement_period')->insert([
                    'employement_status_id' => 3,
                    'employee_id' => 1,
                    'effective_date' => date('Y-m-d'),
                    'employement_period_created_by' => 1,
                    'employement_period_created_at' => date('Y-m-d h:i:s')
                ]);
            }
        }

        $all_leave_management_names = DB::select('SELECT * FROM tbl_leave_management');
        $all_leave_management = json_decode(json_encode($all_leave_management_names), true);
        $leave_entitlements = DB::select('SELECT * FROM tbl_leave_entitlement');
        if(count($get_employees) == 1){
            if(empty($leave_entitlements)){
                foreach($all_leave_management as $leave_entitlement){
                    DB::table('tbl_leave_entitlement')->insert([
                        'leaves_employee_id' => 1,
                        'leave_type_id'=> $leave_entitlement['leave_manage_id'],
                        'leave_allotement' => 10,
                        'leave_ent_created_at' => date('Y-m-d H:i:s'),
                    ]);
                   
                }
            }
        }
       
        $get_employee_permissions = DB::select('SELECT * FROM tbl_employee_permission');
        if(empty($get_employee_permissions)){
            $permissionRecords = [
                '/dashboard',
                '/my-attendance',
                '/my-attendance-request',
                '/my-leaves',
                '/my-interviews',
                '/payslip',
                '/my-meetings',
                '/my-wfh-requests',
                '/employee-resignation',
                '/my-salary-request',
                '/my-loan',
                '/my-tickets',
                '/employees',
                '/employees-managers',
                '/employee-birthdays',
                '/employee-work-anniversay',
                '/gadgets-inventory',
                '/gadgets-allotment',
                '/meetings',
                '/attendance',
                '/attendance-requests',
                '/daily-attendance-report',
                '/wfh-requests',
                '/employees-leaves',
                '/manage-leaves',
                '/interviews',
                '/employee-onboarding',
                '/payroll',
                '/petty-cash',
                '/daily-expense',
                '/loans',
                '/holidays',
                '/departments',
                '/designations',
                '/vehicles',
                '/login-history',
                '/system-settings',
                '/system-error-logs',
                '/resignations',
                '/alerts',
                '/requisition',
                '/my-requisition',
                '/letters',
                '/all-salary-request',
                '/all-loan-request',
                '/all-tickets',
                '/permissions',
                '/tax-slab',
                '/tax-collection',
                '/rooms',
                '/locations',
                '/candiddate-interviews',
                '/employee-devices',
                '/linked-in-post',
                '/shift-management',
                '/employee-expense',
                '/overtime-management',
                '/organogram',
                '/my-expense',
                '/my-shift-time',
                '/employee-letter',
                '/letter-template',
                '/machine-attendance-logs',
                '/leave-management',
                '/polling',
                '/shift-time-request',
                '/employee-bank',
                '/monthly-attendance',
                '/daily-wages-payroll',
                '/employment-status',
                '/attendance-devices',
                '/reports',
                '/attendance-locker',
                '/employee-group',
                '/leave-policy',
                '/deduction-policy',
                '/companies'
            ];
            foreach($permissionRecords as $permission){
                DB::table('tbl_employee_permission')->insert([
                    'module_url' => $permission,
                    'employee_id' => 1,
                    'permission_status' => 1,
                ]);
            }
        }

        $get_shift_management = DB::select('SELECT * FROM tbl_shift_management');
        if(empty($get_shift_management)){
            DB::table('tbl_shift_management')->insert([
                'employee_id' => 1,
                'shift_start_from_date' => date('Y-m-d'),
                'shift_start_time' => '09:00:00',
                'shift_end_time' => '18:00:00',
                'shift_created_by' => 1,
            ]);
        }
        
    }
}
