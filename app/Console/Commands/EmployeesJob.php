<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\{ErrorLog, Employees, Department, Designation, EmployeeSalary, Leave, ShiftManagement, EmploymentPeriod, EmployeeInformation, Holiday, Attendance, Permission, MobileAccess};
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use App\Mail\EmployeesOnboardMail;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Log;
use Exception;

class EmployeesJob extends Command
{
    use NotificationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'EmployeesJob Command Executed Successfully!';

    /**
     * Execute the console command.
     *
     * @return int
     */
    
    public function handle()
    {
        try{
            
            $response = Http::timeout(300)->get('http://sapapi.ismailindustries.com.pk:8080/get-employees');

            if ($response->successful()) {

                $sap_employee_detail = DB::table('sap_employee_detail')
                ->get()->toArray();

                $emailData = []; 
                $emailError = [];

                foreach ($sap_employee_detail as $employee) {

                    $employee_number = $employee->EmployeeNumber;
                    $employee_code = LTRIM($employee->EmployeeNumber, '0');
                    $employee_grade = LTRIM($employee->EmployeeSubGroup, 'Grade ');
                    $employee_group = $employee->BussinesUnit;
                    $employee_name = $employee->EmployeeName;
                    $department_name = $employee->Department;
                    $job_title = $employee->Designation;
                    $business_unit = $employee->PersonalArea;
                    $status_name = $employee->EmployeeGroup;
                    $employee_company = $employee->CompanyName;

                    $employee_company_id = DB::table('tbl_company')
                    ->where('company_name',  $employee_company)
                    ->value('company_id');

                    $group_id = DB::table('tbl_employee_groups')
                    ->where('group_name',  'like', '%' . $employee_group . '%')
                    ->value('employee_group_id');

                    $all_departments = Department::where('department_name', $department_name)
                    ->get(['department_id','department_name'])->toArray();
                    
                    $all_designations = Department::join('tbl_designations', 'tbl_departments.department_id', '=', 'tbl_designations.department_id')
                    ->where('designation_name', 'like', '%' . $employee_grade . '%')
                    ->where('department_name', $department_name)
                    ->get(['designation_id','designation_name'])->toArray();

                    $employee_business_unit = DB::table('tbl_employees')
                    ->where('business_unit', 'like', '%' . $business_unit . '%')
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
                        $add_employee->employee_company =  $employee_company_id ?? 1;
                        $add_employee->employee_department = $all_departments[0]['department_id'];
                        $add_employee->employee_designation = $all_designations[0]['designation_id'];
                        $add_employee->employee_jobtitle = $job_title ?? $all_designations[0]['designation_name'];
                        $add_employee->employee_password = $employee_password;
                        $add_employee->employee_geofencing_enability = 1;
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
                        $add_employee->employee_created_source = 'sap';
                        $add_employee->employee_new_code = $employee_code;
                        $add_employee->employee_created_at =  date('Y-m-d H:i:s');
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

                            $add_mobile_access =  new MobileAccess();
                            $add_mobile_access->mobile_employee_id = $employee_registration_id;
                            $add_mobile_access->access_button_id  = 1;
                            $add_mobile_access->access_buttons = 0;
                            $add_mobile_access->save();

                            $add_mobile_access =  new MobileAccess();
                            $add_mobile_access->mobile_employee_id = $employee_registration_id;
                            $add_mobile_access->access_button_id  = 2;
                            $add_mobile_access->access_buttons = 0;
                            $add_mobile_access->save();
                            
                            $add_mobile_access =  new MobileAccess();
                            $add_mobile_access->mobile_employee_id = $employee_registration_id;
                            $add_mobile_access->access_button_id  = 3;
                            $add_mobile_access->access_buttons = 1;
                            $add_mobile_access->save();

                            $add_mobile_access =  new MobileAccess();
                            $add_mobile_access->mobile_employee_id = $employee_registration_id;
                            $add_mobile_access->access_button_id  = 4;
                            $add_mobile_access->access_buttons = 0;
                            $add_mobile_access->save();

                            $add_mobile_access =  new MobileAccess();
                            $add_mobile_access->mobile_employee_id = $employee_registration_id;
                            $add_mobile_access->access_button_id  = 5;
                            $add_mobile_access->access_buttons = 0;
                            $add_mobile_access->save();

                            $add_mobile_access =  new MobileAccess();
                            $add_mobile_access->mobile_employee_id = $employee_registration_id;
                            $add_mobile_access->access_button_id  = 6;
                            $add_mobile_access->access_buttons = 0;
                            $add_mobile_access->save();

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

                            DB::table('sap_employee_detail')
                            ->where('EmployeeNumber ', $employee_number)
                            ->update(['is_enrolled' => 1]);

                            if($process == 1){
                                
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
                                        $mark_attendance->att_shift_start_time = '09:00:00';
                                        $mark_attendance->att_shift_end_time = '18:00:00';
                                        $mark_attendance->att_check_overall_status = 'Holiday : '.$get_holiday['holiday_name'].'';
                                        $mark_attendance->att_for_date = $day;
                                        $mark_attendance->att_for_month = $month;
                                        $mark_attendance->att_for_year = $year;
                                        $mark_attendance->att_comp_date = $year . '-' . $month . '-' . $day;
                                        $mark_attendance->save();


                                    }
                                }
                                
                                $mypermissions = [

                                    '/dashboard',
                                    '/my-attendance',
                                    '/permissions',
                                ];

                                foreach($mypermissions as $permissionRecord){
                                    $permissions = new Permission();
                                    $permissions->module_url = $permissionRecord;
                                    $permissions->employee_id = $employee_registration_id;
                                    $permissions->permission_status = 1;
                                    $permissions->save();

                                }
                                
                                // $this->send_notification('Welcome To The Team', 'Dear employee, We warmly welcome you in our team. Kindly fill employee detail form by clicking on this notification','personal-information', $employee_registration_id,NULL,NULL,'no');

                                $emailData[$group_id][] = [
                                    'employee_registration_id' => $employee_registration_id,
                                    'employee_code' => $employee_code,
                                    'employee_name' => $employee_name,
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

                // if(!empty($emailData)){
                //     foreach ($emailData as $groupId => $all_email_data) {
                //         if (isset($groupEmailAddresses[$groupId])) {
                //             $toEmail = $groupEmailAddresses[$groupId];
                //             // $ccEmail = ['smalik@ismailindustries.com'];
                //             Mail::to($toEmail)
                //             // ->cc($ccEmail)
                //             ->send(new EmployeesOnboardMail($all_email_data, null));
                //         }   
                //     }
                // }    
            } 

        }catch(Exception $e){

            $generate_error_log = new ErrorLog();
            $generate_error_log->error_log_module = 'Get Sap employees insert hcm';
            $generate_error_log->error_log_action_url = URL::current();
            $generate_error_log->error_log_current_url = URL::previous();
            $generate_error_log->error_log_message = $e->getLine().' '.$e->getMessage();
            $generate_error_log->error_log_generated_employee = 1;
            $generate_error_log->error_log_schedule_time = date('h:i:s a d F, Y');
            $generate_error_log->save();

            $emailError[] = [
                'errormsg' => 'Employees create from SAP to Nexora HCM insertion error. Line: ' . $e->getLine() . '. Message: ' . $e->getMessage(),
            ];

            if(!empty($emailError)){
               
                $toEmail = 'smalik@ismailindustries.com';
                $ccEmail = 'shawaltelecart@gmail.com';
                Mail::to($toEmail)
                ->cc($ccEmail)
                ->send(new EmployeesOnboardMail(null, $emailError));
                
            }    

        }

        $groupEmailAddresses = [
            2 =>  ['mwaqar@ismailindustries.com','manahmad@ismailindustries.com'],
            3 =>  'hmajeed@ismailindustries.com',
            4 =>  ['musiddiqui@ismailindustries.com', 'mwaseem@ismailindustries.com'],
        ];

        if(!empty($emailData)){
            foreach ($emailData as $groupId => $all_email_data) {
                $toEmail = $groupEmailAddresses[$groupId];
                $ccEmail = ['shawaltelecart@gmail.com'];
                Mail::to($toEmail)
                ->cc($ccEmail)
                ->send(new EmployeesOnboardMail($all_email_data, null));
            }
        }    
    }

}
