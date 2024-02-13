<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\{ErrorLog, Employees, Department, Designation, EmployeeSalary, Leave, ShiftManagement, EmploymentPeriod, EmployeeInformation, Holiday, Attendance, Permission};
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use App\Mail\EmployeesOnboardMail;
use App\Traits\NotificationTrait;
use Exception;

class EmployeesGetToSAP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, NotificationTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            
            $response = Http::get('http://sapapi.ismailglobal.com:8080/get-employees');
            
            if ($response->successful()) {
            
                $data = $response->body();

                $sap_employee_detail = DB::table('sap_employee_detail')
                ->get()->toArray();
                
                $emailData = []; 

                foreach ($sap_employee_detail as $employee) {

                    $employee_code = LTRIM($employee->EmployeeNumber, '0');
                    $employee_grade = LTRIM($employee->EmployeeSubGroup, 'Grade ');
                    $employee_group = $employee->BussinesUnit;
                    $employee_name = $employee->EmployeeName;
                    $department_name = $employee->Department;
                    $job_title = $employee->Designation;
                    $business_unit = $employee->PersonalArea;
                    $status_name = $employee->EmployeeGroup;

                    $group_id = DB::table('tbl_employee_groups')
                    ->where('group_name',  'like', '%' . $employee_group . '%')
                    ->value('employee_group_id');

                    $all_departments = Department::where('department_name',  $department_name)
                    ->get(['department_id','department_name'])->toArray();
                    
                    $all_designations = Department::join('tbl_designations', 'tbl_departments.department_id', '=', 'tbl_designations.department_id')
                    ->where('designation_name', 'like', '%' . $employee_grade . '%')
                    ->where('department_name', $department_name)
                    ->get(['designation_id','designation_name'])->toArray();

                    $employee_business_unit = DB::table('tbl_employees')
                    ->where('business_unit', $business_unit)
                    ->value('business_unit');

                    $employee_status_id = DB::table('tbl_employement_status')
                    ->where('employement_status_name', $status_name)
                    ->value('employement_status_id');

                    $employee_password = Crypt::encryptString('123');

                    $employee_doj = $employee->DateOfJoining;

                    $check_if_employees_exist = Employees::where('employee_code', $employee_code)
                    ->get('employee_code')->toArray();
            
                    if(empty($check_if_employees_exist) && !empty($group_id)) {
                    
                        $add_employee = new Employees();
                        $add_employee->employee_code = $employee_code;
                        $add_employee->employee_name = $employee_name;
                        $add_employee->employee_doj =  $employee_doj;
                        $add_employee->employee_cnic = "44444-4444444-2" . $employee_code;
                        $add_employee->employee_personal_email = "test@ismailindustries.com" .  $employee_code;
                        $add_employee->employee_work_email = "test@ismailindustries.com" .  $employee_code;
                        $add_employee->employee_contactno = "0649-8439872" . $employee_code;
                        $add_employee->employee_gender = 'Male';
                        $add_employee->employee_status =  $employee_status_id;
                        $add_employee->employee_company =  1;
                        $add_employee->employee_department = $all_departments[0]['department_id'];
                        $add_employee->employee_designation = $all_designations[0]['designation_id'];
                        $add_employee->employee_jobtitle = $job_title;
                        $add_employee->employee_password = $employee_password;
                        $add_employee->employee_geofencing_enability = false;
                        $add_employee->employee_senior_manager =  1;
                        $add_employee->employee_is_manager = 0;
                        $add_employee->employee_offdays = 'Sunday';
                        $add_employee->employee_jobtype = 'Office';
                        $add_employee->employee_grade =  $employee_grade;
                        $add_employee->employee_group =  $group_id;
                        $add_employee->business_unit =  $employee_business_unit ?? 'NULL';
                        $add_employee->employee_isactive =  1;
                        $add_employee->employee_earning_type =  1;
                        $add_employee->employee_mobile_access =  1;
                        $employee = $add_employee->save();
                        $employee_registration_id = $add_employee->employee_id;

                        if($employee == 1){
                            
                            $emp_salary = new EmployeeSalary();
                            $emp_salary->salary_employee_id = $employee_registration_id;
                            $emp_salary->salary_amount =  25000;
                            $emp_salary->salary_effected_date = $employee_doj;
                            $emp_salary->salary_created_date = date('d-m-Y');
                            $emp_salary->salary_earning_type = 1;
                            $emp_salary->save();

                            $add_leaves =  new Leave();
                            $add_leaves->leaves_employee_id = $employee_registration_id;
                            $add_leaves->leave_type_id      = 1;
                            $add_leaves->leave_allotement   = 0;
                            $add_leaves->leave_ent_created_at =  date('Y-m-d H:i:s');
                            $add_leaves->save();

                            $add_leaves =  new Leave();
                            $add_leaves->leaves_employee_id = $employee_registration_id;
                            $add_leaves->leave_type_id      = 2;
                            $add_leaves->leave_allotement   = 0;
                            $add_leaves->leave_ent_created_at =  date('Y-m-d H:i:s');
                            $add_leaves->save();

                            $add_leaves =  new Leave();
                            $add_leaves->leaves_employee_id = $employee_registration_id;
                            $add_leaves->leave_type_id      = 3;
                            $add_leaves->leave_allotement   = 0;
                            $add_leaves->leave_ent_created_at =  date('Y-m-d H:i:s');
                            $add_leaves->save();

                            $add_shift = new ShiftManagement();
                            $add_shift->employee_id = $employee_registration_id;
                            $add_shift->shift_start_from_date = $employee_doj;
                            $add_shift->shift_start_time = '09:00:00';
                            $add_shift->shift_end_time = '18:00:00';
                            $add_shift->shift_created_by = 1;
                            $add_shift->save();

                            $employment_period = new EmploymentPeriod();
                            $employment_period->employement_status_id = $employee_status_id;
                            $employment_period->employee_id = $employee_registration_id;
                            $employment_period->effective_date = $employee_doj;
                            $employment_period->employement_period_created_by = 1;
                            $employment_period->employement_period_created_at = date('Y-m-d H:i:s');
                            $process = $employment_period->save();

                            if($process == 1){

                                // if(!empty(env('APP_SERVER_URL')) && (env('APP_SERVER_URL') == 'http://iilhcm.ismailglobal.com/')){
                                    
                                    // $toEmail = 'smalik@ismailindustries.com';
                                    // $ccEmail = 'mfaraz@ismailindustries.com';
                                    // Mail::to($toEmail)
                                    //     ->cc($ccEmail)
                                    //     ->send(new EmployeesOnboardMail($employee_registration_id, $employee_code, $employee_name, '123'));
                                // }
                                
                                $submitInformation = new EmployeeInformation();
                                $submitInformation->employeeid = $employee_registration_id;
                                $submitInformation->dob = '2020-01-01';
                                $submitInformation->save();

                                $get_holidays =  Holiday::where('holiday_date','>=', $employee_doj)->where(function ($query) use ($all_departments) {
                                    $query->where('department_name', 'LIKE', '%'.$all_departments[0]['department_id']. '%')
                                    ->orWhere('department_name', 'LIKE', '%' .'*'. '%');
                                })
                                ->get()->toArray();

                                if(!empty($get_holidays)){
                                    foreach($get_holidays as $get_holiday){
                                        $day = date("d", strtotime($get_holiday['holiday_date']));
                                        $month = date("m", strtotime($get_holiday['holiday_date']));
                                        $year = date("Y", strtotime($get_holiday['holiday_date']));

                                        $mark_attendance = new Attendance();
                                        $mark_attendance->att_employee_id =  $employee_registration_id;
                                        $mark_attendance->att_check_in_time = '00:00:00';
                                        $mark_attendance->att_check_out_time = '00:00:00';
                                        $mark_attendance->att_check_in_status = '0';
                                        $mark_attendance->att_check_out_status = 0;
                                        // $mark_attendance->att_shift_start_time = $shift_start_time->input('shift_start_timings').':00';
                                        // $mark_attendance->att_shift_end_time = $shift_end_time->input('shift_end_timings').':00';
                                        $mark_attendance->att_shift_start_time = '09:00:00';
                                        $mark_attendance->att_shift_end_time = '18:00:00';
                                        $mark_attendance->att_check_overall_status = 'Holiday : '.$get_holiday['holiday_name'].'';
                                        $mark_attendance->att_for_date = $day;
                                        $mark_attendance->att_for_month = $month;
                                        $mark_attendance->att_for_year = $year;
                                        $mark_attendance->save();


                                    }
                                }

                                $mypermissions = [

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
                                    '/my-expense',
                                    '/my-shift-time',
                                    '/permissions',
                                ];

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
                                    '/companies',
                                    '/funds',
                                    '/employee-fund',
                                    '/employee-location',
                                    '/sap-attendance'
                                ];

                                foreach($permissionRecords as $permissionRecord ){
                                    $permissions = new Permission();
                                    $permissions->module_url = $permissionRecord;
                                    $permissions->employee_id = $employee_registration_id;
                                    // if($employee_permissions == "No"){
                                    //     $permissions->permission_status = in_array($permissionRecord, $mypermissions) ? 1 : 0;
                                    // }else{
                                        $permissions->permission_status = 0 ;
                                    // }
                                    $permissions->save();

                                }
                                
                                $this->send_notification('Welcome To The Team', 'Dear employee, We warmly welcome you in our team. Kindly fill employee detail form by clicking on this notification','personal-information', $employee_registration_id,NULL,NULL,'no');

                                // $emailData[] = [
                                //     'employee_registration_id' => $employee_registration_id,
                                //     'employee_code' => $employee_code,
                                //     'employee_name' => $employee_name,
                                //     'employee_password' => '123',
                                // ];

                                $emailData[$group_id][] = [
                                    'employee_registration_id' => $employee_registration_id,
                                    'employee_code' => $employee_code,
                                    'employee_name' => $employee_name,
                                    // 'employee_password' => '123',
                                    'employee_department' => $all_departments[0]['department_name'],
                                    'employee_designation' => $all_designations[0]['designation_name'],
                                ];
                            }

                        } 

                    }  
                    
                }

                // $groupEmailAddresses = [
                //     2 =>  'smalik@ismailindustries.com',
                //     3 =>  'smalik@ismailindustries.com',
                //     4 =>  'smalik@ismailindustries.com',
                // ];

                $groupEmailAddresses = [
                    2 =>  'atauseef@ismailindustries.com',
                    3 =>  'hmajeed@ismailindustries.com',
                    4 =>  ['musiddiqui@ismailindustries.com', 'mwaseem@ismailindustries.com'],
                ];

                if(!empty($emailData)){
                    foreach ($emailData as $groupId => $all_email_data) {
                        $toEmail = $groupEmailAddresses[$groupId];
                        $ccEmail = ['oalam@ismailindustries.com', 'saaahmed@ismailindustries.com'];
                        Mail::to($toEmail)
                        ->cc($ccEmail)
                        ->send(new EmployeesOnboardMail($all_email_data));
                    }
                }    
            }   
            // if(!empty($emailData)){
            //     $toEmail = 'smalik@ismailindustries.com';
            //     // $ccEmail = ['mfaraz@ismailindustries.com', 'saaahmed@ismailindustries.com'];
            //     Mail::to($toEmail)
            //     // ->cc($ccEmail)
            //     ->send(new EmployeesOnboardMail($emailData));
            // }
        }catch(Exception $e){
            $generate_error_log = new ErrorLog();
            $generate_error_log->error_log_module = 'Get Sap insert employees';
            $generate_error_log->error_log_action_url = URL::current();
            $generate_error_log->error_log_current_url = URL::previous();
            $generate_error_log->error_log_message = $e->getLine().' '.$e->getMessage();
            $generate_error_log->error_log_generated_employee =  $this->user_logined ?? 1;
            $generate_error_log->error_log_schedule_time = date('h:i:s a d F, Y');
            $generate_error_log->save();
        }
    }
}
