<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\{Employees, Leave, Notification, EmployeeInformation, EmployeeDocument, Department, Designation, Bank, Permission, EmployeeSalary, OnBoardingEmployee, GadgetAllotment, Vehicle, InterviewCandidate, Interview, SystemModule, OfficeLocation, EmployeeDevice,ShiftManagement, HrmSetting,Holiday,Attendance,VehicleAllotment,EmployeeGadget,LeaveManagement,EmploymentStatus,EmploymentPeriod, EmailConfiguration, EmployeeGroup, AttendanceAccess, Company, EmployeeLocation, Fund, EmployeeFund};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait, AttendanceEmployee, MailTrait};
use App\Helpers\ActivityTracker;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Style\Style;
use DateTime;

class EmployeeController extends Controller
{

    use LoginEmployee, LogGenerateTrait ,NotificationTrait,AttendanceEmployee, MailTrait;
    const MAIN_TITLE = "employee";
    const SUB_TITLE  = "employee-managers";
    const BIRTH_TITLE = 'birthdays';
    const ANNIVERSARY_TITLE = 'work-anniversay';
    const DOCUMENT = 'employee-personal-documents';
    const ONBOARDING = 'employee-on-boarding';
    const Devices = 'employee-devices';
    const BANK_TITLE = 'employee-bank';
    const EMP_STATUS = 'employment-status';
    const EMP_GROUP = 'employment-group';
    const PERSONAL_ASSETS = 'employee-company-assets';
    const EMP_LOCATION = 'employee-location';


    protected function index(Request $request){

        try{

            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $where_clause = "1=1";

            if(empty($request->is_active)){
                $where_clause .= ' AND employee_isactive = '."1".'';
            }else{
                if(!empty($request->is_active) && $request->is_active == 1){
                    $where_clause .= ' AND employee_isactive = '."1".''; 
                }
                elseif(!empty($request->is_active) && $request->is_active == 2){
                    $where_clause .= ' AND employee_isactive = '."0".''; 
                }
            }

            // if(!empty($request->input('employee_isactive'))){

            //     $where_clause .= " and employee_isactive=".(($request->input('employee_isactive') == 2) ? 0 : $request->input('employee_isactive'));

            // }else{

            //     $where_clause .= " and employee_isactive=1";

            // }

            if(!empty($request->input('business_unit')) && $request->input('business_unit') != 'All'){

                $where_clause .= " and business_unit= '".$request->input('business_unit')."'";

            }

            if(!empty($request->input('employee_department')) && $request->input('employee_department') != 'All'){

                $where_clause .= " and employee_department= '".$request->input('employee_department')."'";

            }

            if(!empty($request->input('employee_group')) && $request->input('employee_group') != 'All'){

                $where_clause .= " and employee_group= '".$request->input('employee_group')."'";

            }

            // if(!empty($request->input('employee_status'))){

            //     $where_clause .= " and employee_status= '".$request->input('employee_status')."'";

            // }

            $employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->leftJoin('tbl_employee_groups','tbl_employees.employee_group','=','tbl_employee_groups.employee_group_id')
            ->whereRaw($where_clause)
            ->orderBy('employee_code','ASC')
            ->get(['employee_id','employee_code','employee_name','employee_personal_email','employee_department','department_name','designation_name','employee_is_manager','business_unit','group_name'])
            ->toArray();

            $no_of_employees = HrmSetting::get(['no_of_employees'])->toArray();

            $employment_status = EmploymentStatus::get(['employement_status_id','employement_status_name'])->toArray();

            $departments_status = Department::get(['department_id','department_name'])->toArray();

            $employee_active = Employees::where('employee_isactive', 1)->count();

            $employee_groups = EmployeeGroup::get(['employee_group_id','group_name'])->toArray();

            $send_data = compact('title','login_user','employees','no_of_employees','employee_active','employment_status','departments_status','employee_groups');

            if($request->download == "TRUE"){
                
                $employees  = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
                ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
                ->where('employee_isactive','=', 1)
                ->orderBy('employee_id', 'asc')
                ->get(['employee_code as Employee ID','employee_name as Employee Name','department_name as Department','designation_name as Designation', 'employee_grade as Employee Grade' ,'employee_jobtitle as Employee Job Title', 'business_unit as Business Unit'])
                ->toArray();

                $header_style = (new Style())->setFontBold();

                return (new FastExcel($employees))->headerStyle($header_style)->download('Employee-Records.xlsx');
            }else{

                return view('employee.all-employees',$send_data);
                // return view('employee.all-employees2',$send_data);
            }

        }catch(\Exception $e){
            $this->create_error_log('View All Employees', $e);
            $send_data = compact('title');
            return view('errors.500');
        }
        finally{
            ActivityTracker::track_activity('View All Employees');
           
        }

    }

    protected function add_employee(){

        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $all_departments = Department::get()->toArray();
           
            $all_companies = Company::get()->toArray();
            
            $all_locations = OfficeLocation::get()->toArray();
          
            $all_banks = Bank::get()->toArray();
           
            $no_of_employees = HrmSetting::get(['no_of_employees','gross_salary_ratio'])->toArray();
           
            $employee_active = Employees::where('employee_isactive', '=', 1)->count();

            $reporting_managers = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_is_manager', 1)->where('employee_isactive',1)->get(['employee_id','employee_code','employee_name','department_name','designation_name'])->toArray();
            
            $employment_status = EmploymentStatus::get(['employement_status_id','employement_status_name'])->toArray();

            $get_modules = SystemModule::orderBy('module_type','ASC')->get()->toArray();
           
            $randomPassword = Str::random(5);

            $manage_leaves = LeaveManagement::get()->toArray();

            $manage_funds = Fund::get()->toArray();
           
            $employee_group = EmployeeGroup::get()->toArray();

            $email_config = EmailConfiguration::get(['email_config_id'])->toArray();

            $new_employee_id = Employees::select('employee_code')->max('employee_code') + 1;

            $send_data = compact('title','login_user','all_departments','all_banks','reporting_managers','get_modules','all_locations','employee_active','no_of_employees','randomPassword','manage_leaves','employment_status','email_config','employee_group','all_companies','new_employee_id','manage_funds');
            
            if(!empty($no_of_employees && (!empty($employee_active)))){
                if($no_of_employees[0]['no_of_employees'] != "unlimited" && $no_of_employees[0]['no_of_employees'] <= $employee_active ){
                    return redirect()->back();
                }
            }

            return view('employee.add-employee', $send_data);
            // return view('employee.add-employee-new', $send_data);
        }catch(\Exception $e){

            $this->create_error_log('Open Add Employee Page', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Open Add Employee Page');

        }

    }

    protected function create_employee(Request $employee_data){
    
        try{
         
            $locations = OfficeLocation::pluck('location_id')->toArray();

            if($employee_data->input('employee_isactive') == '1'){
                $employee_deactivate_date =  'nullable';
                $employee_deactivate_type =  'nullable';
            }else{
                $employee_deactivate_date =  'required|date_format:Y-m-d';
                $employee_deactivate_type =  'required|numeric|in:1,2';
            }

            // $messages = [
            //     'employee_name.required' => 'The employee name field is required.',
            //     'employee_name.regex' => 'employee name must be character.',
            // ];

            $validator = Validator::make($employee_data->all(), [
                'employee_code' =>  ['required','numeric','unique:tbl_employees,employee_code'],
                'employee_name' =>  'required',
                'employee_contact_no' => ['required','unique:tbl_employees,employee_contactno'],
                'employee_personal_email' => ['required','regex:/^[^@]+@\w+(\.\w+)+$/','unique:tbl_employees,employee_personal_email','unique:tbl_employees,employee_work_email'],
                'employee_official_email' => ['required','regex:/^[^@]+@\w+(\.\w+)+$/','unique:tbl_employees,employee_work_email','unique:tbl_employees,employee_personal_email'],
                'employee_cnic' => ['required','unique:tbl_employees'],
                'employee_gender' => 'required|in:Male,Female',
                'employee_status' => 'required|numeric',
                'employee_grade' => 'required|min:1|max:25|numeric',
                'employee_designation' => 'required',
                'employee_department' => 'required',
                'employee_company' => 'required',
                'employee_jobtitle' => 'required',
                'shift_start_timings' => 'required|date_format:H:i',
                'shift_end_timings' => 'required|date_format:H:i',
                'employee_password' => 'required|max:10',
                'employee_isactive' => 'required|in:0,1',
                'make_ra' => 'required|in:1,0',
                // 'attendance_access' => ($employee_data->input('make_ra') == '1') ? 'required|in:0,1' : '',
                'employee_jobtype' => 'required|in:Office,Home',
                'employee_offday' => ['array','min:0','max:6'],
                'employee_offday.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,',
                'leave_allotment.*' => 'required|numeric|min:0|max:100',
                'leave_manage_id' =>['array','required'],
                'employee_reporting_person' => 'required|numeric',
                'employee_salary' => 'required|min:0|max:99999999|numeric',
                // 'earning_type' => 'required|numeric|in:1,2',
                'emergency_name' => 'string|max:100|nullable',
                'emergency_contact_no' => 'string|min:0|max:50|nullable',
                // 'employee_location' => 'required|in:'.implode(",",$locations),
                // 'employee_location' => 'required',
                'employee_deactivate_date' => $employee_deactivate_date,
                'employee_deactivate_type' => $employee_deactivate_type,
                'employee_group' => 'required|numeric',
                'employee_doj' => 'required|date_format:Y-m-d',
                'employee_dob' => 'required|date_format:Y-m-d|before_or_equal:now',
                'employee_cnic_issue_date' => 'string|min:0|max:50|nullable',
                'employee_cnic_expiry_date' => 'after:employee_cnic_issue_date|nullable',
                'fund_amount.*' => 'required|numeric|min:0|max:99999',
                'fund_id.*' => 'required|numeric'
               
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{

                if(!empty($employee_data->input('employee_location'))){
                    foreach($employee_data->input('employee_location') as $location){
                        $get_office_location = OfficeLocation::where('location_id', $location)->get('location_id')->toArray();
                        if(empty($get_office_location)){
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Location ".$location." doesn't exists in record.",
                            ]);
                        }
                    }
                }
              
                $no_of_employees = HrmSetting::get(['no_of_employees'])->toArray();

                $employee_active = Employees::where('employee_isactive', '=', 1)->count();

                if(!empty($no_of_employees && (!empty($employee_active)))){

                    if($no_of_employees[0]['no_of_employees'] != "unlimited" && $no_of_employees[0]['no_of_employees'] <= $employee_active ){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "You can't add employee(s) because employee(s) limit exceeds.",
                        ]);
                    }
                }

                $employee_mangers = [$employee_data->input('employee_reporting_person')];

                (!empty($employee_data->input('employee_reporting_person2'))) ? array_push($employee_mangers,$employee_data->input('employee_reporting_person2')) : '';

                if($employee_data->input('employee_reporting_person') == $employee_data->input('employee_reporting_person2')){

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You have assigned same reporting manager for both senior and junior. Please select different.",
                    ]);
                }

                $emp_manager = Employees::where('employee_isactive',1)->whereIn('employee_id',$employee_mangers)->get(['employee_id'])->toArray();

                if(count($emp_manager) != count($employee_mangers)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Selected senior / junior manager doesn't exist in active employees.",
                    ]);
                }

                $depratment_name = Department::where('department_id',$employee_data->input('employee_department'))->get('department_name')->toArray();

                if(empty($depratment_name)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Selected department does not exist.",
                    ]);
                }

                $designation_id = Designation::where('designation_id',$employee_data->input('employee_designation'))->get('designation_id')->toArray();

                if(empty($designation_id)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Selected designation does not exist.",
                    ]);
                }

                $employement_status_id = EmploymentStatus::where('employement_status_id',$employee_data->input('employee_status'))->get('employement_status_id')->toArray();

                if(empty($employement_status_id)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Selected employement status does not exist.",
                    ]);
                }

                // $all_employees = Employees::orderBy('employee_id','DESC')->limit(1)->get(['employee_code'])->toArray();
                // if(empty($all_employees)){
                //     $employeeCode = '001';
                // }else{
                //     $employeeCode = '00'. ++ $all_employees[0]['employee_code'];
                // }
               
                $add_employee = new Employees();
                $add_employee->employee_name = $employee_data->input('employee_name');
                $add_employee->employee_code = $employee_data->input('employee_code');
                $add_employee->employee_doj = $employee_data->input('employee_doj');
                $add_employee->employee_cnic = $employee_data->input('employee_cnic');
                $add_employee->cnic_issue_date = $employee_data->input('employee_cnic_issue_date');
                $add_employee->cnic_expiry_date = $employee_data->input('employee_cnic_expiry_date');
                $add_employee->employee_personal_email = $employee_data->input('employee_personal_email');
                $add_employee->employee_work_email = $employee_data->input('employee_official_email');
                $add_employee->employee_contactno = $employee_data->input('employee_contact_no');
                $add_employee->employee_gender = $employee_data->input('employee_gender');
                $add_employee->employee_status = $employee_data->input('employee_status');
                $add_employee->employee_designation = $employee_data->input('employee_designation');
                $add_employee->employee_department = $employee_data->input('employee_department');
                $add_employee->employee_company = $employee_data->input('employee_company');
                $add_employee->business_unit = $employee_data->input('business_unit');
                $add_employee->employee_jobtitle = $employee_data->input('employee_jobtitle');
                $add_employee->employee_shift_flexibilty = $employee_data->input('employee_shift_flexibilty');
                $add_employee->employee_geofencing_enability = ($employee_data->input('employee_geofencing_enability') == '1') ? true : false;
                $add_employee->employee_password = Crypt::encryptString($employee_data->input('employee_password'));
                $add_employee->employee_senior_manager = $employee_data->input('employee_reporting_person');
                $add_employee->employee_junior_manager = $employee_data->input('employee_reporting_person2');
                $add_employee->employee_is_manager = $employee_data->input('make_ra');
                $add_employee->employee_offdays = (!empty($employee_data->input('employee_offday'))) ? implode(',',$employee_data->input('employee_offday')) : '';
                $add_employee->employee_jobtype = $employee_data->input('employee_jobtype');
                // $add_employee->employee_location = $employee_data->input('employee_location');
                $add_employee->employee_isactive = $employee_data->input('employee_isactive');
                $add_employee->employee_earning_type = 1;
                $add_employee->employee_group = $employee_data->input('employee_group');
                $add_employee->employee_deactivate_date = $employee_data->input('employee_deactivate_date');
                $add_employee->employee_deactivation_type = $employee_data->input('employee_deactive_type');
                $add_employee->employee_web_access = $employee_data->input('employee_web_access');
                $add_employee->employee_mobile_access = $employee_data->input('employee_mobile_access');
                
                $employee = $add_employee->save();
                $employee_registration_id = $add_employee->employee_id;

                if($employee == 1){

                    if(!empty($employee_data->input('employee_location'))){
                        foreach($employee_data->input('employee_location') as $location){
                            $new_employee_location = new EmployeeLocation();
                            $new_employee_location->emp_loc_employee_id = $employee_registration_id;
                            $new_employee_location->emp_loc_loc_id = $location;
                            $new_employee_location->created_at = date('Y-m-d H:i:s');
                            $new_employee_location->created_by = session('user_session');
                            $new_employee_location->save();
                        }
                    }

                    $emp_salary = new EmployeeSalary();
                    $emp_salary->salary_employee_id = $employee_registration_id;
                    $emp_salary->salary_amount =  $employee_data->input('employee_salary');
                    $emp_salary->salary_effected_date = $employee_data->input('employee_doj');
                    $emp_salary->salary_created_date = date('d-m-Y');
                    $emp_salary->salary_earning_type = 1;
                    $emp_salary->save();

                    for($i=0;$i<$employee_data->total_count;$i++){

                        $checked_value = 1;
                        $checked_value = $employee_data->input('permission_status_'.$i);

                        if($employee_data->input('employee_jobtype') == 'Home'){
                            if($employee_data->input('module_url_'.$i) == '/wfh-requests' || $employee_data->input('module_url_'.$i) == '/my-wfh-requests'){
                                $checked_value = 0;
                            }
                            if($employee_data->input('employee_department') == 1 || $employee_data->input('employee_department') == 2){
                                if($employee_data->input('module_url_'.$i) == '/wfh-requests'){
                                    $checked_value = 1;
                                }

                            }
                        }
                        if($checked_value == null){
                            $checked_value = 0;
                        }


                        $permissions = new Permission();
                        $permissions->module_url = $employee_data->input('module_url_'.$i);
                        $permissions->employee_id = $employee_registration_id;
                        $permissions->permission_status = $checked_value;
                        $permissions->save();
                    }

                    foreach($employee_data->leave_manage_id as $key => $leave_manage_id){

                        $leave_names = LeaveManagement::where('leave_manage_id',$leave_manage_id)->get('leave_name')->toArray();

                        if(!empty($leave_names)){
                            $add_leaves =  new Leave();
                            $add_leaves->leaves_employee_id = $employee_registration_id;
                            $add_leaves->leave_type_id      = $leave_manage_id;
                            $add_leaves->leave_allotement   = $employee_data->leave_allotment[$key];
                            $add_leaves->leave_ent_created_at =  date('Y-m-d H:i:s');
                            $add_leaves->save();

                        }

                    }
                    if(!empty($employee_data->fund_id)){
                        foreach($employee_data->fund_id as $key => $fund_id){

                            $fund_names = Fund::where('fund_id', $fund_id)->get('fund_name')->toArray();

                            if(!empty($fund_names)){
                                $add_funds =  new EmployeeFund();
                                $add_funds->ef_employee_id = $employee_registration_id;
                                $add_funds->ef_fund_id      = $fund_id;
                                $add_funds->ef_amount   =  $employee_data->fund_amount[$key];
                                $add_funds->ef_created_at =  date('Y-m-d H:i:s');
                                $add_funds->save();

                            }

                        }
                    }    

                    $add_shift = new ShiftManagement();
                    $add_shift->employee_id = $employee_registration_id;
                    $add_shift->shift_start_from_date = $employee_data->input('employee_doj');
                    $add_shift->shift_start_time = $employee_data->input('shift_start_timings').':00';
                    $add_shift->shift_end_time = $employee_data->input('shift_end_timings').':00';
                    $add_shift->shift_created_by = session('user_session');
                    $add_shift->save();

                    $employment_period = new EmploymentPeriod();
                    $employment_period->employement_status_id = $employee_data->input('employee_status');
                    $employment_period->employee_id = $employee_registration_id;
                    $employment_period->effective_date = $employee_data->input('employee_doj');
                    $employment_period->employement_period_created_by = session('user_session');
                    $employment_period->employement_period_created_at = date('Y-m-d H:i:s');
                    $process = $employment_period->save();

                    $add_attendance_access = new AttendanceAccess();
                    $add_attendance_access->access_employee_id = $employee_registration_id;
                    if($employee_data->input('make_ra') == '1'){
                        $add_attendance_access->attendance_access = $employee_data->input('attendance_access');
                    }else{
                        $add_attendance_access->attendance_access = 0;
                    }
                    $add_attendance_access->access_create_at = date('Y-m-d H:i:s');
                    $process = $add_attendance_access->save();

                    if($process == 1){

                        $this->send_notification('Welcome To The Team', 'Dear employee, We warmly welcome you in our team. Kindly fill employee detail form by clicking on this notification','personal-information', $employee_registration_id,NULL,NULL,'no');
                        
                        if(!empty($employee_data->input('employee_personal_email')) && !empty($employee_data->input('send_email'))){
                            
                            $this->sent_email_on_boarding($employee_data->input('employee_personal_email'),$employee_data->input('employee_name'),$employeeCode,$employee_data->input('employee_password'));
                        }

                        if(!empty($employee_data->input('employee_official_email')) && !empty($employee_data->input('send_email'))){
                            if($employee_data->input('employee_official_email') != $employee_data->input('employee_personal_email')){

                                $this->sent_email_on_boarding($employee_data->input('employee_official_email'),$employee_data->input('employee_name'),$employeeCode,$employee_data->input('employee_password'));
                            }
                        }

                        $submitInformation = new EmployeeInformation();
                        $submitInformation->employeeid = $employee_registration_id;
                        $submitInformation->bankname = $employee_data->input('employee_bank_name');
                        $submitInformation->bankcode = $employee_data->input('employee_bank_code');
                        $submitInformation->accounttitle = $employee_data->input('employee_account_title');
                        $submitInformation->accountno = $employee_data->input('employee_account_number');
                        $submitInformation->ibannumber = $employee_data->input('employee_iban');
                        $submitInformation->permanentaddress = $employee_data->input('permanentaddress');
                        $submitInformation->currentaddress = $employee_data->input('permanentaddress');
                        $submitInformation->emergency_contact_no = $employee_data->input('emergency_contact_no');
                        $submitInformation->emergency_name = $employee_data->input('emergency_name');
                        $submitInformation->employee_relation = $employee_data->input('employee_relation');
                        $submitInformation->citizenship = $employee_data->input('employee_nationality');
                        $submitInformation->maritalstatus = $employee_data->input('employee_marital_status');
                        $submitInformation->religion = $employee_data->input('employee_religion');
                        $submitInformation->dob = $employee_data->input('employee_dob');
                        $submitInformation->pob = $employee_data->input('employee_place_of_birth');
                        $submitInformation->save();

                        $itEmployees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('department_name','=','IT')->where('employee_isactive','=',1)->get('employee_id')->toArray();


                        if($employee_data->input('employee_isactive') == 1){
                            if(!empty($itEmployees)){
                                foreach($itEmployees as $employee_it){
                                    $this->send_notification('New Employee Added', 'Dear user, HR added an employee named '.$employee_data->input('employee_name').' in '.$depratment_name['0']['department_name'].' Department. His joining date is '. $employee_data->input('employee_doj').'','gadgets-inventory', $employee_it['employee_id'],NULL,NULL,'no');
                                }
                            }
                        }

                        $get_holidays = Holiday::where('holiday_date','>=', $employee_data->input('employee_doj'))->where(function ($query) use ($employee_data) {
                            $query->where('department_name', 'LIKE', '%'.$employee_data->input('employee_department'). '%')
                            ->orWhere('department_name', 'LIKE', '%' .'*'. '%');
                        })
                        ->get(['holiday_date','holiday_name'])->toArray();

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
                                $mark_attendance->att_check_out_source = 1;
                                $mark_attendance->att_check_out_status = 0;
                                $mark_attendance->att_shift_start_time = $employee_data->input('shift_start_timings').':00';
                                $mark_attendance->att_shift_end_time = $employee_data->input('shift_end_timings').':00';
                                $mark_attendance->att_check_overall_status = 'Holiday : '.$get_holiday['holiday_name'].'';
                                $mark_attendance->att_for_date = $day;
                                $mark_attendance->att_for_month = $month;
                                $mark_attendance->att_for_year = $year;
                                $mark_attendance->save();
                            }
                        }

                        $all_candidates = Interview::join('tbl_interview_candidate','tbl_interview_candidate.candidate_id','=','tbl_interviews.interview_candidate_id')
                        ->join('tbl_employee_onboarding','tbl_employee_onboarding.onboarding_candidate_id','=','tbl_interview_candidate.candidate_id')
                        ->join('tbl_departments','tbl_employee_onboarding.department_name','=','tbl_departments.department_id')
                        ->where('department_id','like','%' .$employee_data->input('employee_department'). '%')
                        ->Where('candidate_name','like','%' .$employee_data->input('employee_name'). '%')->where('onboard_status',0)->count();

                    if($all_candidates > 0){
                        return response()->json([
                            'status' => 'TRUE',
                            'total_candidate' => $all_candidates,
                        ]);
                    }else{
                        $employee_details = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_id',$employee_registration_id)->get(['employee_id','employee_name','employee_gender','employee_password','employee_code','employee_personal_email','department_name','designation_name'])->toArray();

                        return response()->json([
                            'status' => 'TRUE',
                            'employee_data' => $employee_details,
                            'emp_password' => $employee_data->input('employee_password'),
                        ]);
                    }

                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Leaves addition failed. Please try again after some time.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Employee addition failed. Please try again after some time.",
                    ]);
                }
            }

        }catch(\Exception $e){

            $this->create_error_log('Add new employee', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }

        }finally{

            ActivityTracker::track_activity('Add new employee');

        }

    }

    protected function employees_managers(Request $request){
        try{

            $title = Self::SUB_TITLE;

            $login_user = $this->get_login_user_detail();

            $where_clause = '1=1';

            if(!empty($request->employee_department)){
                $where_clause .= ' AND employee_department = "'.$request->employee_department.'"';
            }

            $employees = Employees::join('tbl_departments', 'tbl_departments.department_id', '=', 'tbl_employees.employee_department')
            ->join('tbl_designations', 'tbl_designations.designation_id', '=', 'tbl_employees.employee_designation')
            ->leftJoin('tbl_employees as emp_ra', 'emp_ra.employee_id', '=', 'tbl_employees.employee_senior_manager')
            ->leftJoin('tbl_employees as emp_ra2', 'emp_ra2.employee_id', '=', 'tbl_employees.employee_junior_manager')
            ->whereRaw($where_clause)->where('tbl_employees.employee_isactive', 1)
            ->where('tbl_employees.employee_isactive', 1)
            ->get([
                'tbl_employees.employee_id as employee_id',
                'tbl_employees.employee_code as employee_code',
                'tbl_employees.employee_name as employee_name',
                'emp_ra.employee_id as employee_ra_id',
                'emp_ra.employee_code as employee_ra_code',
                'emp_ra.employee_name as employee_ra_name',
                'emp_ra2.employee_id as employee_ra2_id',
                'emp_ra2.employee_code as employee_ra2_code',
                'emp_ra2.employee_name as employee_ra2_name',
                'tbl_departments.department_name as department_name',
                'tbl_designations.designation_name as designation_name',
                'tbl_employees.employee_is_manager as employee_is_manager'
            ])
            ->toArray();

            $managers = Employees::where('employee_is_manager', 1)->where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name'])->toArray();

            $employee_ids = Employees::where('employee_isactive', 1)->pluck('employee_id')->toArray();

            $departments = Department::get(['department_id','department_name'])->toArray();

            $send_data = compact('title','login_user','employees','managers','departments');

            return view('employee.employee-managers', $send_data);
        }catch(\Exception $e){

            $this->create_error_log('View All Employees Managers', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Employees Managers');
        

        }


    }

    protected function download_employees_managers(Request $request){
        try{

            $security_variable = FALSE;

            $validator = Validator::make($request->all(), [
                'confirmation_code' => ['required']
            ]);

            $login_user = $this->get_login_user_detail();

            $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];

            if($confirmation_code === $request->input('confirmation_code') && $validator->fails() == FALSE){
                $security_variable = TRUE;
            }
            else{
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/employees-managers';</script>";
            }

            $where_clause = '1=1';

            if(!empty($request->employee_department)){
                $where_clause .= ' AND tbl_employees.employee_department = "'.$request->employee_department.'"';
            }

            $employees = Employees::join('tbl_departments', 'tbl_departments.department_id', '=', 'tbl_employees.employee_department')
            ->join('tbl_designations', 'tbl_designations.designation_id', '=', 'tbl_employees.employee_designation')
            ->leftJoin('tbl_employees as emp_ra', 'emp_ra.employee_id', '=', 'tbl_employees.employee_senior_manager')
            ->leftJoin('tbl_employees as emp_ra2', 'emp_ra2.employee_id', '=', 'tbl_employees.employee_junior_manager')
            ->whereRaw($where_clause)
            ->where('tbl_employees.employee_isactive', 1)
            ->get([
                'tbl_employees.employee_id',
                'tbl_employees.employee_code',
                'tbl_employees.employee_name',
                'tbl_employees.employee_designation',
                'tbl_employees.employee_department',
                'emp_ra.employee_name as employee_ra_name',
                'emp_ra2.employee_name as employee_ra2_name',
                'tbl_departments.department_name',
                'tbl_designations.designation_name'
            ])
            ->toArray();

            $managers = Employees::where('employee_is_manager', 1)->where('employee_isactive', 1)->get(['employee_id','employee_code','employee_name'])->toArray();

            $employee_ids = Employees::where('employee_isactive', 1)->pluck('employee_id')->toArray();

            $departments = Department::get(['department_id','department_name'])->toArray();

            $send_data = compact('login_user','employees','managers','departments');
            
            if($security_variable == TRUE){

                $pdf = Pdf::loadView('employee.download-employee-manager-pdf',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Employee(s) Manager report'.'.pdf');
            }

        }catch(\Exception $e){

            $this->create_error_log('Download All Employees Manager pdf', $e);
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('Download All Employees Manager pdf');

        }


    }

    protected function assign_bulk_employee_manager(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'department' => 'required',
                'employee_id' => 'required',
                'manager_type' => 'required|in:Senior,Junior',
                'employee_senior_manager' => ($request->input('employee_senior_manager') == 'Senior' ) ? 'required' : '' ,
                'employee_junior_manager' => ($request->input('employee_junior_manager') == 'Junior' ) ? : '' ,
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                if(!empty($request->input('employee_senior_manager'))){

                    $employee_senior_manager = Employees::where('employee_isactive','=', 1)->where('employee_id', $request->input('employee_senior_manager'))->get(['employee_id','employee_code','employee_name'])->toArray();

                    if(empty($employee_senior_manager)){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Selected senior manager doesn't exist in active employees.",
                        ]);

                    }
                }

                if(!empty($request->input('employee_junior_manager'))){

                    $employee_junior_manager = Employees::where('employee_isactive','=', 1)->where('employee_id', $request->input('employee_junior_manager'))->get(['employee_id','employee_code','employee_name'])->toArray();

                    if(empty($employee_junior_manager)){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Selected junior manager doesn't exist in active employees.",
                        ]);

                    }
                }

                $employee_id = $request->input('employee_id');

                if(!empty($employee_id)){

                    $all_employees = Employees::where('employee_isactive','=', 1)->whereIn('employee_id', $employee_id )->get(['employee_id','employee_code','employee_name','employee_senior_manager','employee_junior_manager'])->toArray();

                    $manager_insert = 1;

                    foreach($all_employees as $employees){

                        $total_team_member = $this->get_employee_hierarchy_unique($employees['employee_id'], 1);

                        if(in_array($request->input('employee_senior_manager'),$total_team_member)){

                            $employee_senior_manager = Employees::where('employee_id', $request->input('employee_senior_manager'))->get(['employee_id','employee_code','employee_name'])->toArray();

                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => $employee_senior_manager[0]['employee_name'].' Cannot be assign as senior manager of '.$employees['employee_name'].' Already exists in its team.',
                            ]);
                        }

                        if(in_array($request->input('employee_junior_manager'),$total_team_member)){

                            $employee_junior_manager = Employees::where('employee_id', $request->input('employee_junior_manager'))->get(['employee_id','employee_code','employee_name'])->toArray();

                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => $employee_junior_manager[0]['employee_name'].' Cannot be assign as junior manager of '.$employees['employee_name'].' Already exists in its team.',
                            ]);
                        }

                        if($employees['employee_id'] != $request->input('employee_senior_manager')){
                            if(!empty($employees['employee_senior_manager']) && !empty($request->input('employee_junior_manager')) && $employees['employee_senior_manager'] == $request->input('employee_junior_manager')){

                                $employee_senior = Employees::where('employee_id', $employees['employee_id'])->get(['employee_id','employee_code','employee_name'])->toArray();

                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => $employee_senior[0]['employee_name']." Junior manager and senior manager cannot be same. Please select different junior Manager.",
                                ]);
                            }

                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => $employees['employee_name']." cannot be manager of their own. Please select different manager.",
                            ]);

                        }
                        if($employees['employee_id'] == $request->input('employee_junior_manager')){

                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => $employees['employee_name']." cannot be manager of their own. Please select different manager.",
                            ]);
                        }
                    }

                    foreach($all_employees as $employees){

                        $employee = Employees::find($employees['employee_id']);

                        if(!empty($employees['employee_junior_manager']) && !empty($request->input('employee_senior_manager')) && $employees['employee_junior_manager'] == $request->input('employee_senior_manager')){
                            $employee->employee_junior_manager = null; 
                        }

                        if($request->input("manager_type") == "Senior"){
                            $employee->employee_senior_manager = $request->input('employee_senior_manager');
                        }else{
                            $employee->employee_junior_manager = $request->input('employee_junior_manager');
                        }

                        $employee->save();

                        $manager_insert += 1;
                    }
                    if($manager_insert > 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Manager updated successfully",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Something went wrong. Please try again.",
                        ]);
                    }
                }else{

                    $all_employees = Employees::where('employee_isactive','=', 1)->whereIn('employee_department', $request->input('department') )->get(['employee_id','employee_code','employee_name','employee_department','employee_senior_manager','employee_junior_manager'])->toArray();

                    $manager_insert = 1;

                    foreach($all_employees as $employees){

                        $total_team_member = $this->get_employee_hierarchy_unique($employees['employee_id'], 1);

                        if(in_array($request->input('employee_senior_manager'),$total_team_member)){

                            $employee_senior_manager = Employees::where('employee_id', $request->input('employee_senior_manager'))->get(['employee_id','employee_code','employee_name'])->toArray();

                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => $employee_senior_manager[0]['employee_name'].' Cannot be assign as senior manager of '.$employees['employee_name'].' Already exists in its team.',
                            ]);
                        }

                        if(in_array($request->input('employee_junior_manager'),$total_team_member)){

                            $employee_junior_manager = Employees::where('employee_id', $request->input('employee_junior_manager'))->get(['employee_id','employee_code','employee_name'])->toArray();

                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => $employee_junior_manager[0]['employee_name'].' Cannot be assign as junior manager of '.$employees['employee_name'].' Already exists in its team.',
                            ]);
                        }

                        if($employees['employee_id'] != $request->input('employee_senior_manager')){
                            if(!empty($employees['employee_senior_manager']) && !empty($request->input('employee_junior_manager')) && $employees['employee_senior_manager'] == $request->input('employee_junior_manager')){

                                $employee_senior = Employees::where('employee_id', $employees['employee_id'])->get(['employee_id','employee_code','employee_name'])->toArray();

                                return response()->json([
                                    'status' => 'FALSE',
                                    'msg' => $employee_senior[0]['employee_name']." Junior manager and senior manager cannot be same. Please select different junior Manager.",
                                ]);
                            }

                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => $employees['employee_name']." cannot be manager of their own. Please select different manager.",
                            ]);

                        }
                        if($employees['employee_id'] == $request->input('employee_junior_manager')){

                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => $employees['employee_name']." cannot be manager of their own. Please select different manager.",
                            ]);
                        }
                    }

                    foreach($all_employees as $employees){

                        $employee = Employees::find($employees['employee_id']);

                        if(!empty($employees['employee_junior_manager']) && !empty($request->input('employee_senior_manager')) && $employees['employee_junior_manager'] == $request->input('employee_senior_manager')){
                            $employee->employee_junior_manager = null; 
                        }

                        if($request->input("manager_type") == "Senior"){
                            $employee->employee_senior_manager = $request->input('employee_senior_manager');
                        }else{
                            $employee->employee_junior_manager = $request->input('employee_junior_manager');
                        }

                        $employee->save();

                        $manager_insert += 1;
                    }
                    if($manager_insert > 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Manager updated successfully",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Something went wrong. Please try again.",
                        ]);
                    }
                }
            }

        }
        catch(\Exception $e){
            $this->create_error_log('Edit Bulk Employees Manager', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }
        finally{
            ActivityTracker::track_activity('Edit Bulk Employees Manager');
        }
	}


    protected function edit_employee($employee_id){

        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $employee_detail = Employees::leftJoin('tbl_leave_entitlement', 'tbl_employees.employee_id', '=', 'tbl_leave_entitlement.leaves_employee_id')->where('employee_id', $employee_id)->get()->toArray();

            $employee_bank =  Employees::join('tbl_personal_informations', 'tbl_employees.employee_id', '=','tbl_personal_informations.employeeid')->where('employee_id', $employee_id)->get(['employee_id','employee_name','employee_code','information_id','bankname','bankcode','accountno','accounttitle','ibannumber','permanentaddress','currentaddress','emergency_contact_no','emergency_name','employee_relation','citizenship','maritalstatus','dob','pob','religion'])->toArray();

            $employee_salary = Employees::join('tbl_employee_salary', 'tbl_employees.employee_id', '=', 'tbl_employee_salary.salary_employee_id')->where('employee_id', $employee_id)->orderBy('salary_id','DESC')->get('salary_amount')->toArray();

            $employee_salary_detail = Employees::join('tbl_employee_salary', 'tbl_employees.employee_id', '=', 'tbl_employee_salary.salary_employee_id')->where('employee_id', $employee_id)->get(['employee_name','salary_amount','salary_effected_date','salary_created_date','salary_earning_type'])->toArray();

            $employee_department = Employees::join('tbl_departments', 'tbl_employees.employee_department', '=', 'tbl_departments.department_id')->where('employee_id', $employee_id)->get('department_id')->toArray();

            $department_id =  $employee_department[0]['department_id'];

            $employee_designation_detail = Designation::where('department_id', $department_id)->get()->toArray();

            $all_companies = Company::get()->toArray();

            $employment_status = EmploymentStatus::get(['employement_status_id','employement_status_name'])->toArray();

            $employment_period = EmploymentPeriod::where('employee_id',$employee_id)->orderBy('employement_period_id','DESC')->get(['effective_date'])->toArray();

            $secondLastEffectiveDate = EmploymentPeriod::where('employee_id',$employee_id)->skip(1)->take(1)->get(['effective_date'])->toArray();

            $employment_period_count  = EmploymentPeriod::where('employee_id',$employee_id)->count();

            $employee_locations = EmployeeLocation::where('emp_loc_employee_id', $employee_id)->get()->toArray();

            $all_departments = Department::get()->toArray();

            $all_locations = OfficeLocation::get()->toArray();

            $all_banks = Bank::get()->toArray();

            $get_modules = SystemModule::get()->toArray();

            $no_of_employees = HrmSetting::get(['no_of_employees','gross_salary_ratio'])->toArray();

            $get_permissions = Permission::where('employee_id',$employee_id)->where('permission_status',1)->pluck('module_url')->toArray();

            $relations = ['Father','Mother','Brother','Sister','Guardian'];
           
            $reporting_managers = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_is_manager',1)->where('employee_isactive',1)->get(['employee_id','employee_code','employee_name','department_name','designation_name'])->toArray();

            $total_team_member = $this->get_employee_hierarchy_unique($employee_id, 1);

            $attendance_count = Attendance::where('att_employee_id',$employee_id)->whereNot('att_check_overall_status','LIKE', '%Holiday%')->count();

            $shift_start_timings = $this->get_employee_today_shift($employee_id,"in",date('Y-m-d'));
            $shift_end_timings = $this->get_employee_today_shift($employee_id,"out",date('Y-m-d'));

            $fetch_shift_timing = ShiftManagement::where('employee_id',$employee_id)
                ->get([ 'shift_start_from_date', 'shift_start_time', 'shift_end_time', 'shift_created_by','shift_id'])->toArray();

            $leaves_entitled = LeaveManagement::
            leftjoin('tbl_leave_entitlement', function($join) use($employee_id){
                $join->on('tbl_leave_management.leave_manage_id','=', 'tbl_leave_entitlement.leave_type_id');
                $join->where('leaves_employee_id',$employee_id);
            })->get(['leave_manage_id','leave_allotement','leave_ent_created_at','leave_name'])->toArray();

            
            $employee_funds = Fund::
            leftjoin('tbl_employees_funds', function($join) use($employee_id){
                $join->on('tbl_funds.fund_id','=', 'tbl_employees_funds.ef_fund_id');
                $join->where('ef_employee_id',$employee_id);
            })->get(['fund_id','ef_amount','ef_created_at','fund_name'])->toArray();
           
            $employement_status_history = EmploymentPeriod::join('tbl_employees','tbl_employees.employee_id','=','tbl_employement_period.employee_id')->join('tbl_employement_status','tbl_employement_status.employement_status_id','=','tbl_employement_period.employement_status_id')->where('tbl_employement_period.employee_id',$employee_id)->orderBy('employement_period_id','ASC')->get(['employee_name','employement_status_name','effective_date'])->toArray();

            $get_attendance_access = AttendanceAccess::where('access_employee_id', $employee_id)->get()->toArray();
           
            $employee_groups = EmployeeGroup::get()->toArray();

            $send_data = compact('title','login_user','employee_detail','employee_department','department_id','employee_designation_detail','all_departments','all_banks','reporting_managers','employee_salary','employee_salary_detail','all_locations','total_team_member','employee_bank','get_permissions','get_modules','attendance_count','shift_start_timings','shift_end_timings','fetch_shift_timing','leaves_entitled','employment_status','employment_period','secondLastEffectiveDate','employment_period_count','employement_status_history','relations','employee_id','get_attendance_access','employee_groups','all_companies','employee_locations','no_of_employees','employee_funds');

            return view('employee.edit-employee', $send_data);
        }catch(\Exception $e){

            $this->create_error_log('View Edit Employee Page', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View Edit Employee Page');

        }
    }

    protected function update_employee(Request $employee_data){
        try{
          
            $locations = OfficeLocation::pluck('location_id')->toArray();

            $secondLastEffectiveDate = EmploymentPeriod::where('employee_id',$employee_data->input('employee_id'))->skip(1)->take(1)->get(['effective_date','employement_status_id'])->toArray();

            if(empty($secondLastEffectiveDate)){
                $employment_period = EmploymentPeriod::where('employee_id',$employee_data->input('employee_id'))->orderBy('employement_period_id','DESC')->get(['effective_date','employement_status_id'])->toArray();

                $minDate =  $employment_period[0]['effective_date'];
                $lastStatus =  $employment_period[0]['employement_status_id'];
            }else{
                $minDate = $secondLastEffectiveDate[0]['effective_date'];
                $lastStatus =  $secondLastEffectiveDate[0]['employement_status_id'];
            }

            if($employee_data->input('employee_isactive') == '0'){
                $employee_deactivate_date =  'required';
                $employee_deactivate_type =  'required|numeric|in:1,2';
              
            }else{
                $employee_deactivate_date =  'nullable';
                $employee_deactivate_type =  'nullable';
            }

            // $messages = [
            //     'employee_name.required' => 'The employee name field is required.',
            //     'employee_name.regex' => 'employee name must be character.',
            // ];
           
            $validator = Validator::make($employee_data->all(), [
                'employee_name' =>  'required',
                'employee_contact_no' => ['required','unique:tbl_employees,employee_contactno,'.$employee_data->input('employee_id').',employee_id'],
                'employee_personal_email' => ['required','regex:/^[^@]+@\w+(\.\w+)+$/','unique:tbl_employees,employee_personal_email,'.$employee_data->input('employee_id').',employee_id','unique:tbl_employees,employee_work_email,'.$employee_data->input('employee_id').',employee_id'],
                'employee_official_email' => ['required','regex:/^[^@]+@\w+(\.\w+)+$/','unique:tbl_employees,employee_work_email,'.$employee_data->input('employee_id').',employee_id','unique:tbl_employees,employee_personal_email,'.$employee_data->input('employee_id').',employee_id'],
                'employee_cnic' => ['required', 'unique:tbl_employees,employee_cnic,'.$employee_data->input('employee_id').',employee_id'],
                'employee_gender' => 'required|in:Male,Female',
                'employee_status' => 'required|numeric',
                'employee_grade' => 'required|min:1|max:25|numeric',
                'employee_designation' => 'required',
                'employee_department' => 'required',
                'employee_company' => 'required',
                'employee_jobtitle' => 'required',
                'employee_password' => 'required',
                'employee_isactive' => 'required|in:1,0',
                'make_ra' => 'required|in:1,0',
                // 'attendance_access' => ($employee_data->input('make_ra') == '1') ? 'required|in:0,1' : '',
                'employee_jobtype' => 'required|in:Office,Home',
                'employee_offday' => ['array','min:0','max:6'],
                'employee_offday.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,',
                'employee_reporting_person' => 'required|numeric',
                'employee_salary' => 'required|min:0|max:99999999|numeric',
                // 'earning_type' => 'required|numeric|in:1,2',
                'shift_start_timings' => 'required|date_format:H:i',
                'shift_end_timings' => 'required|date_format:H:i',
                'emergency_name' => 'string|max:100|nullable',
                'emergency_contact_no' => 'string|min:0|max:50|nullable',
                // 'employee_location' => 'required|in:'.implode(",",$locations),
                // 'employee_location' => 'required',
                'employment_status_date' => ($lastStatus == $employee_data->input('employee_status')) ? 'required|date_format:Y-m-d' : 'required|date_format:Y-m-d|after_or_equal:'.$minDate,
                'employee_deactivate_date'=> $employee_deactivate_date,
                'employee_deactive_type'=> $employee_deactivate_type,
                'leave_allotment.*' => 'required|numeric|min:0|max:100',
                'employee_group' => 'required|numeric',
                'employee_doj' => 'required|date_format:Y-m-d',
                'employee_dob' => 'required|date_format:Y-m-d|before_or_equal:now',
                'employee_cnic_issue_date' => 'string|min:0|max:50|nullable',
                'employee_cnic_expiry_date' => 'after:employee_cnic_issue_date|nullable',
                'fund_amount.*' => 'required|numeric|min:0|max:99999',
                'fund_id.*' => 'required|numeric'
                
            ]);
            
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }

            else{

                if(!empty($employee_data->input('employee_location'))){
                    foreach($employee_data->input('employee_location') as $location){
                        $get_office_location = OfficeLocation::where('location_id', $location)->get('location_id')->toArray();
                        if(empty($get_office_location)){
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Location ".$location." doesn't exists in record.",
                            ]);
                        }
                    }
                }

                $no_of_employees = HrmSetting::get(['no_of_employees'])->toArray();

                $employee_active = Employees::where('employee_isactive', '=', 1)->count();

                $check_employee_active =  Employees::where('employee_id', $employee_data->input('employee_id'))->get('employee_isactive')->toArray();

                if($check_employee_active[0]['employee_isactive'] == 1){
                    $employee_active -= 1;
                }

                if(!empty($no_of_employees && (!empty($employee_active)))){

                    if($no_of_employees[0]['no_of_employees'] != "unlimited" && $no_of_employees[0]['no_of_employees'] <= $employee_active && $employee_data->input('employee_isactive') == 1 ){

                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "You can't update employee because employee limit exceeds.",
                        ]);
                    }
                }

                $depratment_name = Department::where('department_id',$employee_data->input('employee_department'))->get('department_name')->toArray();

                $employee_mangers = [$employee_data->input('employee_reporting_person')];

                (!empty($employee_data->input('employee_reporting_person2'))) ? array_push($employee_mangers,$employee_data->input('employee_reporting_person2')) : '';

                if($employee_data->input('employee_reporting_person') == $employee_data->input('employee_reporting_person2')){

                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You have assigned same reporting manager for both senior and junior. Please select different.",
                    ]);
                }

                $employement_status_id = EmploymentStatus::where('employement_status_id',$employee_data->input('employee_status'))->get('employement_status_id')->toArray();

                if(empty($employement_status_id)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Selected employement status does not exist.",
                    ]);
                }

                $emp_manager = Employees::where('employee_isactive',1)->whereIn('employee_id',$employee_mangers)->get(['employee_id'])->toArray();

                if(count($emp_manager) != count($employee_mangers)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Selected senior / junior manager doesn't exist in active employees.",
                    ]);
                }

                if(empty($depratment_name)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Selected department does not exist.",
                    ]);
                }

                $designation_id = Designation::where('designation_id',$employee_data->input('employee_designation'))->get('designation_id')->toArray();

                if(empty($designation_id)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Selected designation does not exist.",
                    ]);
                }


                $total_team_member = $this->get_employee_hierarchy_unique($employee_data->input('employee_id'),1);

                if(in_array($employee_data->input('employee_reporting_person'),$total_team_member) || in_array($employee_data->input('employee_reporting_person2'),$total_team_member)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Cannot be assign as manager of this employee. Already exists in its team.',
                    ]);
                }


                $update_employee_code = Employees::where('employee_code',$employee_data->input('employee_code'))->where('employee_id','!=',$employee_data->input('employee_id'))->get()->toArray();

                if(!empty($update_employee_code)){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Employee code already assign ".$update_employee_code[0]['employee_name'],
                    ]);
                }else{
                    $attendance_count = Attendance::where('att_employee_id',$employee_data->input('employee_id'))->whereNot('att_check_overall_status','LIKE', '%Holiday%')->count();
                    // dd($employee_data->input('employee_id'));
                    $update_employee = Employees::find($employee_data->input('employee_id'));
                    // dd($update_employee);
                    $employee_old_doj = $update_employee->employee_doj;
                    $employee_old_make_ra = $update_employee->employee_is_manager;
                    $employee_old_department = $update_employee->employee_department;

                    if($attendance_count > 0){
                        $update_employee->employee_doj =  $employee_old_doj;
                    }else{
                        $update_employee->employee_doj = $employee_data->input('employee_doj');
                    }

                    $employment_period = EmploymentPeriod::where('employee_id',$employee_data->input('employee_id'))->orderBy('employement_period_id','DESC')->get(['effective_date','employement_status_id'])->toArray();

                    // $update_employee->employee_code = $employee_data->input('employee_code');
                    $update_employee->employee_name = $employee_data->input('employee_name');
                    $update_employee->employee_cnic = $employee_data->input('employee_cnic');
                    $update_employee->cnic_issue_date = $employee_data->input('employee_cnic_issue_date');
                    $update_employee->cnic_expiry_date = $employee_data->input('employee_cnic_expiry_date');
                    $update_employee->employee_personal_email = $employee_data->input('employee_personal_email');
                    $update_employee->employee_work_email = $employee_data->input('employee_official_email');
                    $update_employee->employee_contactno = $employee_data->input('employee_contact_no');
                    $update_employee->employee_gender = $employee_data->input('employee_gender');
                    $update_employee->employee_status = $employee_data->input('employee_status');
                    $update_employee->employee_designation = $employee_data->input('employee_designation');
                    $update_employee->employee_department = $employee_data->input('employee_department');
                    $update_employee->employee_company = $employee_data->input('employee_company');
                    $update_employee->business_unit = $employee_data->input('business_unit');
                    $update_employee->employee_password = Crypt::encryptString($employee_data->input('employee_password'));
                    $update_employee->employee_shift_flexibilty = $employee_data->input('employee_shift_flexibilty');
                    $update_employee->employee_geofencing_enability = ($employee_data->input('employee_geofencing_enability') == '1') ? true : false;
                    $update_employee->employee_senior_manager = $employee_data->input('employee_reporting_person');
                    $update_employee->employee_junior_manager = $employee_data->input('employee_reporting_person2');
                    $update_employee->employee_is_manager = $employee_data->input('make_ra');
                    $update_employee->employee_isactive = $employee_data->input('employee_isactive');
                    $update_employee->employee_earning_type = 1;
                    $update_employee->employee_deactivate_date = $employee_data->input('employee_deactivate_date');
                    $update_employee->employee_offdays = (!empty($employee_data->input('employee_offday'))) ? implode(',',$employee_data->input('employee_offday')) : '';
                    $update_employee->employee_jobtype = $employee_data->input('employee_jobtype');
                    // $update_employee->employee_location = $employee_data->input('employee_location');
                    $update_employee->employee_grade = $employee_data->input('employee_grade');
                    $update_employee->employee_group = $employee_data->input('employee_group');
                    $update_employee->employee_deactivation_type = $employee_data->input('employee_deactive_type');
                    $update_employee->employee_web_access = $employee_data->input('employee_web_access');
                    $update_employee->employee_mobile_access = $employee_data->input('employee_mobile_access');
                    
                    if($employee_data->input('employee_isactive') == 0){

                        $check_vehicle_allots = VehicleAllotment::where('allotment_employee_id',$employee_data->input('employee_id'))->whereNull('allotment_cancellation_date')->get(['allotment_employee_id','allotment_cancellation_date','allotment_id'])->toArray();

                        foreach($check_vehicle_allots as $check_vehicle_allot){
                            $employee_vehicle_update = VehicleAllotment::find($check_vehicle_allot['allotment_id']);
                            $employee_vehicle_update->allotment_cancellation_date =  $employee_data->input('employee_deactivate_date');
                            $employee_vehicle_update->save();
                        }

                        EmployeeDevice::where('device_employee_id',$employee_data->input('employee_id'))->update(['device_status' => 2]);
                        
                        if($employee_data->input('make_ra') == '1'){

                            $all_employee = Employees::where('employee_senior_manager','=',$employee_data->input('employee_id'))->orWhere('employee_junior_manager','=',$employee_data->input('employee_id'))->count();

                            if($all_employee > 0){

                                return response()->json([
                                    'status' => 'TRUE',
                                    'total_employee' => $all_employee,
                                    'deactivate_date' => $employee_data->input('employee_deactivate_date'),
                                    'deactivation_type' => $employee_data->input('employee_deactive_type'),
                                ]);
                            }

                        }
                    }


                    if($employee_old_make_ra == '1' && $employee_data->input('make_ra') == '0'){

                        $all_employee = Employees::where('employee_senior_manager','=',$employee_data->input('employee_id'))->orWhere('employee_junior_manager','=',$employee_data->input('employee_id'))->count();

                        if($all_employee > 0){

                            return response()->json([
                                'status' => 'TRUE',
                                'total_employee' => $all_employee,
                                'employee_is_manager' => $employee_data->input('make_ra'),

                            ]);
                        }

                    }
                    
                    $update_employee->save();
                    if(!empty($employee_data->input('employee_location'))){
                        
                        EmployeeLocation::where('emp_loc_employee_id',$employee_data->input('employee_id'))->whereNotIn('emp_loc_loc_id',$employee_data->input('employee_location'))->delete();
                      
                        foreach($employee_data->input('employee_location') as $location){
                            $check_if_emp_location_exists = EmployeeLocation::where('emp_loc_employee_id',$employee_data->input('employee_id'))->where('emp_loc_loc_id',$location)->get()->toArray();
                            
                            if(empty($check_if_emp_location_exists)){
                                $new_employee_location = new EmployeeLocation();
                                $new_employee_location->emp_loc_employee_id = $employee_data->input('employee_id');
                                $new_employee_location->emp_loc_loc_id = $location;
                                $new_employee_location->created_at = date('Y-m-d H:i:s');
                                $new_employee_location->created_by = session('user_session');
                                $new_employee_location->save();
                            }
                        }
                    }else{
                        EmployeeLocation::where('emp_loc_employee_id',$employee_data->input('employee_id'))->delete();
                    }

                    
                    $itEmployees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->where('department_name','=','IT')->where('employee_isactive','=',1)->get('employee_id')->toArray();

                    if($employee_data->input('employee_isactive') == 0){

                        $where_clause = 'gadget_status = 1 and tbl_gadget_allotment.allotment_id in (select max(allotment_id) from tbl_gadget_allotment group by gadget_id  )';
                        
                        $gadget_allotments_active = GadgetAllotment::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_gadget_allotment.allotment_employee_id')
                        ->join('tbl_employees_gadgets', 'tbl_employees_gadgets.gadget_id', '=', 'tbl_gadget_allotment.gadget_id')
                        ->whereRaw($where_clause)
                        ->where('employee_id', $employee_data->input('employee_id'))
                        ->orderBy('allotment_id','DESC')
                        ->pluck('tbl_employees_gadgets.gadget_id')
                        ->toArray();

                        $gadget_allotments = GadgetAllotment::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_gadget_allotment.allotment_employee_id')
                        ->join('tbl_employees_gadgets', 'tbl_employees_gadgets.gadget_id', '=', 'tbl_gadget_allotment.gadget_id')
                        ->where('employee_id', $employee_data->input('employee_id'))
                        ->orderBy('allotment_id','DESC')
                        ->get(['tbl_employees_gadgets.gadget_id'])
                        ->toArray();

                        foreach($gadget_allotments as $gadget){
                            if(in_array($gadget['gadget_id'],$gadget_allotments_active)){
                                $update_gadget = EmployeeGadget::find($gadget['gadget_id']);
                                $update_gadget->gadget_status = 2;
                                $update_gadget->save();
                            }
                        }

                        $update_employees = Employees::find($employee_data->input('employee_id'));

                        $update_employees->employee_deactivate_date = $employee_data->input('employee_deactivate_date');
                        $update_employees->save();
                        if(!empty($itEmployees)){
                            foreach($itEmployees as $employee_it){

                                $this->send_notification('Employee Deactivate','Dear user, HR deactivate an employee named '.$employee_data->input('employee_name').' in '.$depratment_name['0']['department_name'].' Department. His deactivate date is '.$employee_data->input('employee_deactivate_date').'','gadgets-inventory', $employee_it['employee_id'],NULL,NULL,'no');

                            }
                        }
                    }
                    $update_employees = Employees::find($employee_data->input('employee_id'));
                    if(!empty($update_employees->employee_deactivation_type) && !empty($update_employees->employee_deactivate_date) && !empty($employee_data->input('employee_isactive') == 1)){


                        foreach($itEmployees as $employee_it){

                            $this->send_notification('Employee Activated', 'Dear user, HR again activate an employee named '.$employee_data->input('employee_name').' in '.$depratment_name['0']['department_name'].' Department.','gadgets-inventory', $employee_it['employee_id'],NULL,NULL,'no');
                        }

                        $update_employees->employee_deactivation_type = '';
                        $update_employees->employee_deactivate_date = '';
                        $update_employees->save();

                    }
                    foreach($employee_data->leave_manage_id as $key=>$leave_entitlement_detail){

                        $leave_names = LeaveManagement::where('leave_manage_id',$leave_entitlement_detail)->get('leave_name')->toArray();

                        if(!empty($leave_names)){
                            if(!empty($employee_data->leave_ent_created_at[$key])){

                                $date = $employee_data->leave_ent_created_at[$key];
                                $updated_date = date('Y-m-d H:i:s');
                            }else{
                                $date =  date('Y-m-d H:i:s');
                                $updated_date = '';
                            }
                            $leave = Leave::updateOrCreate([
                                'leaves_employee_id' => $employee_data->input('employee_id'),
                                'leave_type_id' =>  $leave_entitlement_detail
                            ], [
                                'leave_allotement' => $employee_data->leave_allotment[$key],
                                'leave_ent_created_at' => $date,
                                'leave_ent_updated_at' => $updated_date
                            ]);
                        }

                    }
                    if(!empty($employee_data->fund_id)){

                        foreach($employee_data->fund_id as $key => $fund_detail_id){

                            $funds_name = Fund::where('fund_id', $fund_detail_id)->get('fund_name')->toArray();

                            if(!empty($funds_name)){

                                if(!empty($employee_data->ef_created_at[$key])){
                                    $date = $employee_data->ef_created_at[$key];
                                }else{
                                    $date =  date('Y-m-d H:i:s');
                                }

                                $employee_fund = EmployeeFund::updateOrCreate([
                                    'ef_employee_id' => $employee_data->input('employee_id'),
                                    'ef_fund_id' => $fund_detail_id
                                ], [
                                    'ef_amount' => $employee_data->fund_amount[$key],
                                    'ef_created_at' => $date
                                ]);
                            }

                        }
                    }    

                    $updateInformation = EmployeeInformation::find($employee_data->input('information_id'));
                    $updateInformation->bankname = $employee_data->input('bank_name');
                    $updateInformation->bankcode = $employee_data->input('bank_code');
                    $updateInformation->accounttitle = $employee_data->input('account_title');
                    $updateInformation->accountno = $employee_data->input('account_number');
                    $updateInformation->ibannumber = $employee_data->input('account_iban');
                    $updateInformation->permanentaddress = $employee_data->input('permanentaddress');
                    $updateInformation->currentaddress = $employee_data->input('permanentaddress');
                    $updateInformation->emergency_contact_no = $employee_data->input('emergency_contact_no');
                    $updateInformation->emergency_name = $employee_data->input('emergency_name');
                    $updateInformation->employee_relation = $employee_data->input('employee_relation');
                    $updateInformation->citizenship = $employee_data->input('employee_nationality');
                    $updateInformation->maritalstatus = $employee_data->input('employee_marital_status');
                    $updateInformation->religion = $employee_data->input('employee_religion');
                    $updateInformation->dob = $employee_data->input('employee_dob');
                    $updateInformation->pob = $employee_data->input('employee_place_of_birth');
                    $process = $updateInformation->save();

                    Permission::where('employee_id',$employee_data->input('employee_id'))->delete();

                    for($i=0;$i<$employee_data->total_count;$i++){

                        $checked_value = 1;
                        $checked_value = $employee_data->input('permission_status_'.$i);

                        if($employee_data->input('employee_jobtype') == 'Home'){
                            if($employee_data->input('module_url_'.$i) == '/wfh-requests' || $employee_data->input('module_url_'.$i) == '/my-wfh-requests'){
                                $checked_value = 0;
                            }
                            if($employee_data->input('employee_department') == 1 || $employee_data->input('employee_department') == 2){
                                if($employee_data->input('module_url_'.$i) == '/wfh-requests'){
                                    $checked_value = 1;
                                }

                            }
                        }
                        if($checked_value == null){
                            $checked_value = 0;
                        }
                        $permissions = new Permission();
                        $permissions->module_url = $employee_data->input('module_url_'.$i);
                        $permissions->employee_id = $employee_data->input('employee_id');
                        $permissions->permission_status = $checked_value;
                        $process = $permissions->save();

                    }
                    $employee_status_date = $employee_data->input('employment_status_date');
                    if($attendance_count == 0){

                        $get_employee_shift = ShiftManagement::where('employee_id',$update_employee->employee_id)->get()->toArray();
                        $get_holidays =  Holiday::where('holiday_date','>=', $employee_data->input('employee_doj'))->where(function ($query) use ($employee_data) {
                            $query->where('department_name', 'LIKE', '%'.$employee_data->input('employee_department'). '%')
                            ->orWhere('department_name', 'LIKE', '%' .'*'. '%');
                        })
                        ->get()->toArray();
                        if($employee_old_doj != $employee_data->input('employee_doj')){
                            $employee_status_date =  $employee_data->input('employee_doj');

                            EmploymentPeriod::where('employee_id',$employee_data->input('employee_id'))->delete();
                            $update_employees_salaries = EmployeeSalary::where('salary_employee_id',$employee_data->input('employee_id'))->get(['salary_effected_date','salary_created_date','salary_id'])->toArray();
                            if(!empty($update_employees_salaries)){
                                $update_employee_salary = EmployeeSalary::find($update_employees_salaries[0]['salary_id']);
                                $update_employee_salary->salary_effected_date = $employee_data->input('employee_doj');
                                $update_employee_salary->salary_created_date = date('d-m-Y');
                                $update_employee_salary->save();

                            }

                            if(!empty($get_employee_shift)){

                                $delete_record_of_same_day = ShiftManagement::where('employee_id',$employee_data->input('employee_id'))->where('shift_start_from_date',date('Y-m-d',strtotime($employee_old_doj)))->delete();

                                $insert_shift_start_timings = $employee_data->input('shift_start_timings').':00';
                                $insert_shift_end_timings = $employee_data->input('shift_end_timings').':00';


                                $add_shift = new ShiftManagement();
                                $add_shift->employee_id =  $update_employee->employee_id;
                                $add_shift->shift_start_from_date = $employee_data->input('employee_doj');
                                $add_shift->shift_start_time = $insert_shift_start_timings;
                                $add_shift->shift_end_time = $insert_shift_end_timings;
                                $add_shift->shift_created_by =  session('user_session');
                                $process =  $add_shift->save();

                                ShiftManagement::where('employee_id',$update_employee->employee_id)->where('shift_start_from_date','<',$employee_data->input('employee_doj'))->delete();
                                Attendance::where('att_employee_id',$update_employee->employee_id)->delete();
                                if(!empty($get_holidays)){

                                    foreach($get_holidays as $get_holiday){
                                        $day = date("d", strtotime($get_holiday['holiday_date']));
                                        $month = date("m", strtotime($get_holiday['holiday_date']));
                                        $year = date("Y", strtotime($get_holiday['holiday_date']));

                                        $mark_attendance = new Attendance();
                                        $mark_attendance->att_employee_id = $update_employee->employee_id;
                                        $mark_attendance->att_check_in_time = '00:00:00';
                                        $mark_attendance->att_check_out_time = '00:00:00';
                                        $mark_attendance->att_check_in_status = '0';
                                        $mark_attendance->att_check_out_status = 0;
                                        $mark_attendance->att_shift_start_time =  $get_employee_shift[0]['shift_start_time'];
                                        $mark_attendance->att_shift_end_time = $get_employee_shift[0]['shift_end_time'];
                                        $mark_attendance->att_check_overall_status = 'Holiday : '.$get_holiday['holiday_name'].'';
                                        $mark_attendance->att_for_date = $day;
                                        $mark_attendance->att_for_month = $month;
                                        $mark_attendance->att_for_year = $year;
                                        $mark_attendance->save();
                                    }
                                }
                            }

                            $old_effected_date = EmployeeSalary::where('salary_employee_id',$update_employee->employee_id)->orderBy('salary_id','ASC')->get(['salary_id'])->toArray();

                            $update_salary = EmployeeSalary::find($old_effected_date[0]['salary_id']);
                            $update_salary->salary_effected_date = $employee_data->input('employee_doj');
                            $update_salary->save();

                        }else{
                            if($employee_old_department != $employee_data->input('employee_department')){

                                $get_attendance = Attendance::where('att_employee_id',$employee_data->input('employee_id'))->delete();

                                if(!empty($get_holidays)){

                                    foreach($get_holidays as $get_holiday){
                                        $day = date("d", strtotime($get_holiday['holiday_date']));
                                        $month = date("m", strtotime($get_holiday['holiday_date']));
                                        $year = date("Y", strtotime($get_holiday['holiday_date']));

                                        $mark_attendance = new Attendance();
                                        $mark_attendance->att_employee_id = $update_employee->employee_id;
                                        $mark_attendance->att_check_in_time = '00:00:00';
                                        $mark_attendance->att_check_out_time = '00:00:00';
                                        $mark_attendance->att_check_in_status = '0';
                                        $mark_attendance->att_check_out_status = 0;
                                        $mark_attendance->att_shift_start_time =  $get_employee_shift[0]['shift_start_time'];
                                        $mark_attendance->att_shift_end_time = $get_employee_shift[0]['shift_end_time'];
                                        $mark_attendance->att_check_overall_status = 'Holiday : '.$get_holiday['holiday_name'].'';
                                        $mark_attendance->att_for_date = $day;
                                        $mark_attendance->att_for_month = $month;
                                        $mark_attendance->att_for_year = $year;
                                        $mark_attendance->save();

                                    }
                                }
                            }
                        }
                    }else{
                        if($employee_old_department != $employee_data->input('employee_department')){

                            $get_attendance = Attendance::whereRaw('CONCAT(att_for_year,"-",att_for_month,"-",att_for_date) >= "'.date("Y-m-d").'"')->where('att_employee_id',$employee_data->input('employee_id'))->delete();

                            $get_holidays =  Holiday::where('holiday_date','>=',date('Y-m-d'))->where(function ($query) use ($employee_data) {
                                $query->where('department_name', 'LIKE', '%'.$employee_data->input('employee_department'). '%')
                                ->orWhere('department_name', 'LIKE', '%' .'*'. '%');
                            })
                            ->get()->toArray();

                            if(!empty($get_holidays)){

                                foreach($get_holidays as $get_holiday){
                                    $day = date("d", strtotime($get_holiday['holiday_date']));
                                    $month = date("m", strtotime($get_holiday['holiday_date']));
                                    $year = date("Y", strtotime($get_holiday['holiday_date']));

                                    $employee_shift_start = $this->get_employee_today_shift($employee_data->input('employee_id'),"in",date('Y-m-d'));
                                    $employee_shift_end = $this->get_employee_today_shift($employee_data->input('employee_id'),"out",date('Y-m-d'));

                                    $mark_attendance = new Attendance();
                                    $mark_attendance->att_employee_id = $employee_data->input('employee_id');
                                    $mark_attendance->att_check_in_time = '00:00:00';
                                    $mark_attendance->att_check_out_time = '00:00:00';
                                    $mark_attendance->att_check_in_status = '0';
                                    $mark_attendance->att_check_out_status = 0;
                                    $mark_attendance->att_shift_start_time =  $employee_shift_start;
                                    $mark_attendance->att_shift_end_time =  $employee_shift_end;
                                    $mark_attendance->att_check_overall_status = 'Holiday : '.$get_holiday['holiday_name'].'';
                                    $mark_attendance->att_for_date = $day;
                                    $mark_attendance->att_for_month = $month;
                                    $mark_attendance->att_for_year = $year;
                                    $mark_attendance->save();

                                }
                            }
                        }
                    }

                    $shift_start_timings = $this->get_employee_today_shift($employee_data->input('employee_id'),"in",date('Y-m-d'));
                    $shift_end_timings = $this->get_employee_today_shift($employee_data->input('employee_id'),"out",date('Y-m-d'));

                    if(($employee_data->input('shift_end_timings') != $shift_end_timings || $employee_data->input('shift_start_timings') != $shift_start_timings) && $employee_old_doj == $employee_data->input('employee_doj')  ){


                        if(strtotime($employee_data->input('employee_doj')) <= strtotime(date('Y-m-d',strtotime('+1 days')))){

                            $insert_shift_start_timings = $employee_data->input('shift_start_timings').':00';
                            $insert_shift_end_timings = $employee_data->input('shift_end_timings').':00';

                            $delete_record_of_same_day = ShiftManagement::where('employee_id',$employee_data->input('employee_id'))->where('shift_start_from_date',date('Y-m-d',strtotime('+1 days')))->delete();

                            $get_todays_attendance = Attendance::where('att_for_date', date('d'))->where('att_for_month', date('m'))->where('att_for_year', date('Y'))->where('att_employee_id', $employee_data->input('employee_id'))->get(['attendance_id'])->toArray();

                            $add_shift = new ShiftManagement();
                            $add_shift->employee_id = $employee_data->input('employee_id');
                            $add_shift->shift_start_from_date = (empty($get_todays_attendance)) ? date('Y-m-d') : date('Y-m-d',strtotime('+1 days'));
                            $add_shift->shift_start_time = $insert_shift_start_timings;
                            $add_shift->shift_end_time = $insert_shift_end_timings;
                            $add_shift->shift_created_by = session('user_session');
                            $add_shift->save();

                        }else{

                            $insert_shift_start_timings = $employee_data->input('shift_start_timings').':00';
                            $insert_shift_end_timings = $employee_data->input('shift_end_timings').':00';

                            $delete_record_of_same_day = ShiftManagement::where('employee_id',$employee_data->input('employee_id'))->where('shift_start_from_date',date('Y-m-d',strtotime($employee_old_doj)))->delete();

                            $add_shift = new ShiftManagement();
                            $add_shift->employee_id = $employee_data->input('employee_id');
                            $add_shift->shift_start_from_date = date('Y-m-d',strtotime($employee_data->input('employee_doj')));
                            $add_shift->shift_start_time = $insert_shift_start_timings;
                            $add_shift->shift_end_time = $insert_shift_end_timings;
                            $add_shift->shift_created_by = session('user_session');
                            $add_shift->save();

                        }

                    }

                    if($employment_period[0]['effective_date'] != $employee_data->input('employment_status_date')  || $employment_period[0]['employement_status_id'] != $employee_data->input('employee_status') || $employee_old_doj != $employee_data->input('employee_doj')  ){

                        $employment_period = new EmploymentPeriod();
                        $employment_period->employement_status_id = $employee_data->input('employee_status');
                        $employment_period->employee_id = $update_employee->employee_id;
                        $employment_period->effective_date =  $employee_status_date;
                        $employment_period->employement_period_created_by = session('user_session');
                        $employment_period->employement_period_created_at = date('Y-m-d H:i:s');
                        $process = $employment_period->save();
                    }

                    if($employee_data->input('make_ra') == '1'){
                       $attendance_access = AttendanceAccess::where('access_employee_id', $update_employee->employee_id)->update(['attendance_access' => $employee_data->input('attendance_access') ]);
                    }elseif($employee_data->input('make_ra') == '0'){
                        $attendance_access = AttendanceAccess::where('access_employee_id', $update_employee->employee_id)->update(['attendance_access' => 0 ]);
                    }else{
                        $attendance_access = AttendanceAccess::where('access_employee_id', $update_employee->employee_id)->update(['attendance_access' => 0 ]);

                    }

                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Employee details updated successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Employee addition failed. Please try again after some time.",
                        ]);
                    }
                }
            }


        }
        catch(\Exception $e){
            $this->create_error_log("Update Employee's Details", $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }
        finally{
            ActivityTracker::track_activity("Update Employee's Details");
        }

    }

    protected function assign_manager(Request $request){

        try{
            $employee_id = $request->input('employee_id');

            $total_team_member = $this->get_employee_hierarchy_unique($employee_id, 1);

            if(in_array($request->input('senior_manager'),$total_team_member) || in_array($request->input('junior_manager'),$total_team_member)){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Cannot be assign as manager of this employee. Already exists in its team.',
                ]);
            }


            $employee = Employees::find($employee_id);
            $employee_data = json_decode(json_encode($employee), TRUE);
            if($employee_id != $request->input('senior_manager') && $employee_id != $request->input('junior_manager')){
                if($employee_data['employee_junior_manager'] != $request->input('senior_manager') && $employee_data['employee_senior_manager'] != $request->input('junior_manager') ){
                    $employee->employee_senior_manager = $request->input('senior_manager');
                    $employee->employee_junior_manager = $request->input('junior_manager');
                    $process = $employee->save();
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Manager updated successfully",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Something went wrong. Please try again.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Senior manager and junior manager cannot be same. Please select different Senior Manager",
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Employee cannot be manager of their own. Please select different manager.",
                ]);
            }
        }
        catch(\Exception $e){
            $this->create_error_log('Edit Manager', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }
        finally{
            ActivityTracker::track_activity('Edit Manager');
        }
	}

    protected function employees_birthdays(Request $request){

        try{
            $title = Self::BIRTH_TITLE;

            $login_user = $this->get_login_user_detail();

            if(!empty($request->input('mob'))){
                $month = $request->input('mob');
            }else{
                $month = date('m');
            }

            $birthdays = Employees::join('tbl_personal_informations', 'tbl_employees.employee_id', '=', 'tbl_personal_informations.employeeid')->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_isactive','=',1)->whereNotNull('dob')->whereMonth('dob','=',$month)->orderByRaw('DAY(dob)')->get(['employee_id','employee_code','employee_name','employee_department','employee_designation','profile_avatar','employeeid','dob','department_name','designation_name'])->toArray();

            $send_data = compact('title','login_user','birthdays');

            return view('employee.employee-birthdays', $send_data);
        }
        catch(\Exception $e){

            $this->create_error_log('View All Employees Birthdays', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Employees Birthdays');


        }
	}

    protected function download_employees_birthdays(Request $request){

        try{

            $security_variable = FALSE;

            $validator = Validator::make($request->all(), [
                'confirmation_code' => ['required']
            ]);

            $login_user = $this->get_login_user_detail();

            $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];

            if($confirmation_code === $request->input('confirmation_code') && $validator->fails() == FALSE){
                $security_variable = TRUE;
            }
            else{
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/employee-birthdays';</script>";
            }

            if(!empty($request->input('mob'))){
                $month = $request->input('mob');
            }else{
                $month = date('m');
            }

            $birthdays = Employees::join('tbl_personal_informations', 'tbl_employees.employee_id', '=', 'tbl_personal_informations.employeeid')->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_isactive','=',1)->whereNotNull('dob')->whereMonth('dob','=',$month)->orderByRaw('DAY(dob)')->get(['employee_id','employee_code','employee_name','employee_department','employee_designation','profile_avatar','employeeid','dob','department_name','designation_name'])->toArray();

            $send_data = compact('login_user','birthdays');

            if($security_variable == TRUE){

                $pdf = Pdf::loadView('employee.download-employee-bithday-pdf',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Employee(s) Birthday detail'.'.pdf');
            }


        }
        catch(\Exception $e){

            $this->create_error_log('Download All Employees Birthdays pdf', $e);
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Download All Employees Birthdays pdf');

        }
	}


    protected function employee_anniversary(Request $request){

        try{
            $title = Self::ANNIVERSARY_TITLE;

            $login_user = $this->get_login_user_detail();

            if(!empty($request->input('m'))){

                $current_month = $request->input('m');

            }else{

                $current_month = date('m');

            }

            $employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('employee_isactive', 1)
            ->whereRaw('MONTH(employee_doj) = ?', $current_month)
            ->whereRaw('DATEDIFF(DAY, employee_doj, GETDATE()) >= 365')
            ->orderByRaw('DAY(employee_doj)')
            ->get(['employee_id','employee_code','department_name','designation_name','employee_doj'])
            ->toArray();

            $send_data = compact('title','login_user','employees');

            return view('employee.employee-work-anniversary', $send_data);

        }catch(\Exception $e){

            $this->create_error_log('View All Employees Work Anniversary', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Employees Work Anniversary');

        }

    }

    protected function download_employee_anniversary(Request $request){

        try{

           $security_variable = FALSE;

            $validator = Validator::make($request->all(), [
                'confirmation_code' => ['required']
            ]);

            $login_user = $this->get_login_user_detail();

            $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];

            if($confirmation_code === $request->input('confirmation_code') && $validator->fails() == FALSE){
                $security_variable = TRUE;
            }
            else{
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/employee-work-anniversay';</script>";
            }

            if(!empty($request->input('m'))){

                $current_month = $request->input('m');

            }else{

                $current_month = date('m');

            }

            $employees = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('employee_isactive', 1)
            ->whereRaw('MONTH(employee_doj) = ?', $current_month)
            ->whereRaw('DATEDIFF(DAY, employee_doj, GETDATE()) >= 365')
            ->orderByRaw('DAY(employee_doj)')
            ->get(['employee_id','employee_code','employee_name','department_name','designation_name','employee_doj'])
            ->toArray();

            $send_data = compact('login_user','employees');

            if($security_variable == TRUE){

                $pdf = Pdf::loadView('employee.download-employee-work-anniversary-pdf',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Employee(s) Work Anniversary detail'.'.pdf');
            }

        }catch(\Exception $e){

            $this->create_error_log('Download All Employees Work Anniversary', $e);
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Download All Employees Work Anniversary pdf');

        }

    }


    protected function employee_documents($employee_id){
        try{

            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $employee_data = Employees::where('employee_id', $employee_id)->get(['employee_name'])->toArray();

            $document_attachments = EmployeeDocument::where('employee_id', $employee_id)->orderBy('document_id','DESC')->get()->toArray();
           
            $send_data = compact('title','employee_data','login_user','document_attachments');

            if(is_numeric($employee_id) && !empty($employee_data)){
                return view('employee.employee-documents', $send_data);
            }else{
                abort(404);
            }
        }catch(\Exception $e){

            $this->create_error_log('View Employee Documents', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{
            ActivityTracker::track_activity('Access Employee Documents Module');
           
        }

    }

    protected function store_employee_documents(Request $request){
       
        try{

            $filename = '';
            $condition = 'min:0';

            if(!empty($request->file('file'))){

                foreach($request->file('file') as $file){

                    $filename = $file->getClientOriginalExtension();

                }
                $condition = 'min:'.count($request->input('title'));
            }


            $validator = Validator::make($request->all(), [
                'title.*'    => 'required',
                'file' => ['required','array',$condition],
                'file.*' =>  ['required','max:10000',($filename == 'txt') ?
                File::types(['jpg','jpeg','png','docx','pdf','txt'])
                :
                File::types(['jpg','jpeg','png','docx','pdf','txt'])],

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $i=0;
                if($request->hasFile('file')){
                    foreach($request->file('file') as $file){
                        $document = $file;
                        $document_name =  $this->removeSpecialCharacters($document->getClientOriginalName());

                        $j=1;
                        while(is_file(storage_path('app/portal_documents/employee_docs/'.$request->input('employee_id').'/'.$document_name))){
                            $document_name = $j.$document_name;
                            $j++;
                        }

                        $document->storeAs('portal_documents/employee_docs/'.$request->input('employee_id').'/', $document_name);

                        $document_upload = new EmployeeDocument();
                        $document_upload->employee_id = $request->input('employee_id');
                        $document_upload->document_title = $request->input('title')[$i];
                        $document_upload->document_name = $document_name;
                        $document_upload->document_upload_date = date('h:i:s a d F, Y');
                        $document_upload->document_uploaded_by = session('user_session');
                        $process = $document_upload->save();

                        $i++;

                    }
                    $this->send_notification('New Document Uploaded','Dear employee, HR uploaded a new document. Please click here to view.','employee-personal-documents?document-id='.$document_upload->document_id, $document_upload['employee_id'],null,null,'no');

                    return response()->json([
                        'status' => 'TRUE',
                        'msg'   =>"Documents uploaded successfully. Please wait.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg'   => "Documents are required",
                    ]);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log('Store Employee Documents', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }finally{
            ActivityTracker::track_activity('Store Employee Documents');
        }

    }

    protected function edit_upload_document(Request $request){
       try{

       
            $filename = '';
        
            if(!empty($request->file('file'))){
                $filename =$request->file('file')->getClientOriginalExtension(); 
            }
            $validator = Validator::make($request->all(), [
                'title'    => 'required',
                'file' =>  ['max:10000',($filename == 'txt') ?
                File::types(['jpg','jpeg','png','docx','pdf','txt'])
                :
                File::types(['jpg','jpeg','png','docx','pdf','txt'])],

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {
                $document_upload = EmployeeDocument::find($request->input('document_id'));
                if(!empty($document_upload)){
                    $document_upload->document_title = $request->input('title');
                    if($request->hasFile('file')){

                        if (file_exists(storage_path('app/portal_documents/employee_docs/'.$document_upload->employee_id.'/'.$document_upload->document_name))) {
                        
                            unlink(storage_path('app/portal_documents/employee_docs/'.$document_upload->employee_id.'/'.$document_upload->document_name));
                        }

                        $file_name =  $this->removeSpecialCharacters($request->file('file')->getClientOriginalName());

                        $path =  $request->file('file')->storeAs('portal_documents/employee_docs/'.$document_upload->employee_id.'/',$file_name);

                        $document_upload->document_name = $file_name;

                    }

                    $process = $document_upload->save();

                    if($process == 1){


                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Document updated successfully.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Document updation failed. Please try again after some time.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "This document has been deleted.",
                    ]);
                }    
            }  
        }catch(\Exception $e){
            $this->create_error_log('Update Employee Documents', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }finally{
            ActivityTracker::track_activity('Update Employee Documents');
        } 
            
    }

    protected function store_employee_personal_documents(Request $request){

        try{

            $filename = '';
            $condition = 'min:0';

            if(!empty($request->file('file'))){

                foreach($request->file('file') as $file){

                    $filename = $file->getClientOriginalExtension();

                }
                $condition = 'min:'.count($request->input('title'));
            }


            $validator = Validator::make($request->all(), [
                'title.*'    => 'required',
                'file' => ['required','array',$condition],
                'file.*' =>  ['required','max:10000',($filename == 'txt') ?
                File::types(['jpg','jpeg','png','docx','pdf','txt'])
                :
                File::types(['jpg','jpeg','png','docx','pdf','txt'])],

            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else {

                $login_user = $this->get_login_user_detail();

                $i=0;
                if($request->hasFile('file')){
                    foreach($request->file('file') as $file){
                        $document = $file;
                        $document_name = $document->getClientOriginalName();

                        $j=1;
                        while(is_file(storage_path('app/portal_documents/employee_docs/'.$login_user[0]['employee_id'].'/'.$document_name))){
                            $document_name = $j.$document_name;
                            $j++;
                        }

                        $document->storeAs('portal_documents/employee_docs/'.$login_user[0]['employee_id'].'/', $document_name);

                        $document_upload = new EmployeeDocument();
                        $document_upload->employee_id = $login_user[0]['employee_id'];
                        $document_upload->document_title = $request->input('title')[$i];
                        $document_upload->document_name = $document_name;
                        $document_upload->document_upload_date = date('h:i:s a d F, Y');
                        $document_upload->document_uploaded_by = $login_user[0]['employee_id'];
                        $process = $document_upload->save();

                        $i++;
                    }

                    $this->send_notification('New Document Upload','Dear HR, '.$login_user[0]['employee_name'].', uploaded a new document Please click here to view.','employees/employee-documents/'.$login_user[0]['employee_id'].'?document-id='.$document_upload->document_id,null,null,null,'yes');

                    return response()->json([
                        'status' => 'TRUE',
                        'msg'   =>"Documents uploaded successfully. Please wait.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg'   => "Documents are required",
                    ]);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log('Store Employee personal Documents', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }finally{
            ActivityTracker::track_activity('Store Employee personal Documents');
        }

    }

    protected function delete_employee_document(Request $request_data){

        if($request_data['status'] == "Delete"){
            try{

                $employee_document_ids = explode(",", $request_data['ids']);
               
                $deleted_documents_count = 0;

                foreach ($employee_document_ids as $employee_document_id) {
                    $delete_employee_document = EmployeeDocument::find($employee_document_id);

                    if(!empty($delete_employee_document)){

                        if (!empty($delete_employee_document)) {
                            $document_path = storage_path('app/portal_documents/employee_docs/' . $delete_employee_document->employee_id . '/' . $delete_employee_document->document_name);

                            if (file_exists($document_path)) {
                                unlink($document_path);
                            }

                            $delete_employee_document->delete();
                            $deleted_documents_count++;
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => 'This employee document(s) has been deleted.',
                        ]);
                    }    
                }
               
                if($deleted_documents_count > 0) {
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Employee document(s) have been deleted',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'This employee document(s) has been deleted.',
                    ]);
                }


            }catch(\Exception $e){
                $this->create_error_log('Delete Employee Document', $e);
                if(!env('APP_DEBUG')){
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Something went wrong. Please try again.",

                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Something went wrong. Please try again.",
                        'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                    ]);

                }

            }

            finally{
                ActivityTracker::track_activity('Delete Employee Document');

            }
        }   
    }

    protected function change_password_function(Request $request_data){

        try{

            $validator = Validator::make($request_data->all(), [
                'current_password' => 'required',
                'new_password' => 'required|max:10',
                'confirm_new_password' => 'required|max:10',
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{

                $login_user = $this->get_login_user_detail();

                $current_password = $request_data->input('current_password');
                $new_password = $request_data->input('new_password');
                $confirm_password = $request_data->input('confirm_new_password');

                if(Crypt::decryptString($login_user[0]['employee_password']) == $current_password){

                    if($new_password == $confirm_password){

                        $update_employee_password = Employees::find(session('user_session'));
                        $update_employee_password->employee_password = Crypt::encryptString($confirm_password);
                        $update_employee_password->save();

                        return response()->json([
                            'status' => 'TRUE',
                            'msg'   =>"Password changed successfully. Please wait.",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg'   =>"Confirm password not match. Please try again",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg'   =>"Wrong current password entered. Please try again",
                    ]);
                }
            }
        }catch(\Exception $e){
            $this->create_error_log('Change Account Password', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }finally{
            ActivityTracker::track_activity('Change Account Password');
        }

	}

    public function get_employee_location(Request $request){


        $login_user = $this->get_login_user_detail();

        if($request->status == "2"){
            $status = 0;
        }else{
            $status = 1;
        }

        $where_clause = '1=1';

        if(!empty($request->employee_location)){

            $where_clause .= " AND employee_location = '".$request->employee_location."'";
        }

        if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department']== 2 || $login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department']== 3){

            $get_employees = Employees::where('employee_isactive','=',$status)->whereRaw($where_clause)->get(['employee_id','employee_code','employee_name'])->toArray();

            $result[] = '<option value="">Select Name</option>';
            if(!empty($get_employees)){
                foreach($get_employees as $employee){
                    $result[] .= '<option value="'.$employee['employee_id'].'">'.$employee['employee_code']. ' - '. $employee['employee_name'].'</option>';
                }
            }
        }else{

            $login_employee_id = $login_user[0]['employee_id'];

            $total_team_member = $this->get_employee_hierarchy_unique($login_employee_id, $status);

            $get_employees = Employees::where('employee_isactive','=',$status)->whereRaw($where_clause)->whereIn('employee_id', $total_team_member)->get(['employee_id','employee_code','employee_name'])->toArray();
            $result[] = '<option value="">Select Name</option>';
            if(!empty($get_employees)){
                foreach($get_employees as $employee){
                    $result[] .= '<option value="'.$employee['employee_id'].'">'.$employee['employee_code']. ' - '. $employee['employee_name'].'</option>';
                }
            }

        }
        return $result;
	}

    protected function get_employees_proxy($status){


		if($status == '2'){
            $status = "0";
        }
        $get_employees = Employees::where('employee_isactive','=',$status)->where('employee_id','!=',session('user_session'))->get(['employee_id','employee_code','employee_name'])->toArray();
        $result[] = '<option value="">Select Name</option>';
		if(!empty($get_employees)){
			foreach($get_employees as $employee){
				$result[] .= '<option value="'.$employee['employee_id'].'">'.$employee['employee_code']. ' - '. $employee['employee_name'].'</option>';
			}
		}
        return $result;
	}

    protected function employee_personal_documents(){

        try{
            $title = Self::DOCUMENT;

            $login_user = $this->get_login_user_detail();

            $documents = EmployeeDocument::where('employee_id',$login_user[0]['employee_id'])->orderBy('document_id','DESC')->get()->toArray();

            $send_data = compact('title','documents','login_user');

            return view('employee.employee-personal-documents', $send_data);

        }catch(\Exception $e){

            $this->create_error_log('List Employee Personal Documents', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Access Employee Personal Documents Module');
          
        }

    }

    protected function update_employee_salary(Request $employeeData){

        try{

            $validator = Validator::make($employeeData->all(), [
                'effected_date' => 'required|date_format:Y-m-d',
                'salary_amount' => 'required|min:0|max:99999999|numeric' ,
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{
                $employee_doj = Employees::where('employee_id',$employeeData->input('employee_id'))->get('employee_doj')->toArray();

                $payroll_exists = $this->check_if_payroll_exist($employeeData->input('effected_date'),$employeeData->input('employee_id'),$custom_message = NULL);

                if($payroll_exists['status'] == TRUE){
                    return response()->json([
                        'msg' => $payroll_exists['msg'],
                        'status' => 'FALSE'
                    ]);
                }

                $check_attendance_sheet_availability = $this->check_if_attendance_sheet_locked($employeeData->input('effected_date'));

                if($check_attendance_sheet_availability['status'] == FALSE){
                    return response()->json([
                        'msg' => $check_attendance_sheet_availability['msg'],
                        'status' => 'FALSE'
                    ]);
                }

                if($employeeData->input('effected_date') >= $employee_doj[0]['employee_doj']){

                    $earning_type = Employees::where('employee_id',$employeeData->input('employee_id'))->get(['employee_earning_type'])->toArray();

                    $insert_employees_salary = new EmployeeSalary();
                    $insert_employees_salary->salary_employee_id = $employeeData->input('employee_id');
                    $insert_employees_salary->salary_amount = $employeeData->input('salary_amount');
                    $insert_employees_salary->salary_created_date = date('d-m-Y');
                    $insert_employees_salary->salary_effected_date = $employeeData->input('effected_date');
                    $insert_employees_salary->salary_earning_type = $earning_type[0]['employee_earning_type'];
                    $process = $insert_employees_salary->save();
                    if($process == 1){
                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => "Employee salary updated successfully",
                        ]);
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Something went wrong. Please try again.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "You can't update the salary before employee joining date : " . $employee_doj[0]['employee_doj'],
                    ]);
                }

            }

        }
        catch(\Exception $e){

            $this->create_error_log('Update Employee Salary', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }

        }
        finally{

            ActivityTracker::track_activity('Update Employee Salary');

        }

    }

    protected function get_employee_onboading(Request $request){

        try{
            $title = Self::ONBOARDING;

            $login_user = $this->get_login_user_detail();

            $dynamic_years = $this->dynamic_year_drop_down();

            $current_month_year = '1=1';
            if(!empty($request->input('m')) && !empty($request->input('y'))){

                $current_month_year = 'DATE_FORMAT(doj,"%Y-%m") = "'.$request->input('y').'-'.$request->input('m').'"';

            }

            $employees_onboardings = OnBoardingEmployee::join('tbl_interview_candidate','tbl_employee_onboarding.onboarding_candidate_id','tbl_interview_candidate.candidate_id')
            ->join('tbl_departments','tbl_employee_onboarding.department_name','=','tbl_departments.department_id')->whereRaw($current_month_year)->where('onboard_status',0)->orderBy('doj','ASC')->get(['onboarding_id','candidate_name','tbl_departments.department_name','doj'])->toArray();


            $send_data = compact('title','login_user','employees_onboardings','dynamic_years');

            return view('employee.employee-on-boarding',$send_data);
        }catch(\Exception $e){

            $this->create_error_log('View All OnBoard Employees', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All OnBoard Employees');

        }

    }

    protected function update_employee_onboarding(Request $request){

        try{
           $update_onboard_employee = OnBoardingEmployee::find($request->input('onboarding_id'));

           if(!empty($update_onboard_employee)){
                $update_onboard_employee->doj =  $request->input('joining_date');
                $update_onboard_employee->employee_hired_created_at = date('Y-m-d H:i:s');
                $process =  $update_onboard_employee->save();

                if($process == 1){

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Onboarding employee updated successfully.",
                    ]);
                }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Onboarding employee not updated successfully try again!",
                        ]);
                }
           }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Onboarding employee id not found",
                ]);
            }


        }catch(\Exception $e){

            $this->create_error_log('Edit OnBoard Employees', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }

        }finally{

            ActivityTracker::track_activity('Edit OnBoard Employees');

        }

    }


    protected function delete_employee_on_boarding($boarding_id){
        try{

            $get_onbord_employee = OnBoardingEmployee::where('onboarding_id', $boarding_id)->get()->toArray();
            if(!empty($get_onbord_employee)){
                OnBoardingEmployee::where('onboarding_id', $boarding_id)->delete();
            }else{
                return redirect('/employee-onboarding');
            }

        }
        catch(\Exception $e){
            $this->create_error_log("Delete On Board Employee", $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }
        finally{
            ActivityTracker::track_activity('Delete On Board Employee');
            return redirect('employee-onboarding');
        }
    }


    protected function employee_allotment($employee_id){

        try{
            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $employee_data = Employees::where('employee_id', $employee_id)->get()->toArray();

            $where_clause = 'gadget_status = 1 and tbl_gadget_allotment.allotment_id in (select max(allotment_id) from tbl_gadget_allotment group by gadget_id  )';
            $gadget_allotments_active = GadgetAllotment::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_gadget_allotment.allotment_employee_id')
            ->join('tbl_employees_gadgets', 'tbl_employees_gadgets.gadget_id', '=', 'tbl_gadget_allotment.gadget_id')
            ->whereRaw($where_clause)
            ->where('employee_id', $employee_id)
            ->orderBy('allotment_id','DESC')
            ->pluck('allotment_id')
            ->toArray();

            $gadget_allotments = GadgetAllotment::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_gadget_allotment.allotment_employee_id')
            ->join('tbl_employees_gadgets', 'tbl_employees_gadgets.gadget_id', '=', 'tbl_gadget_allotment.gadget_id')
            ->where('employee_id', $employee_id)
            ->orderBy('allotment_id','DESC')
            ->get()
            ->toArray();

            $get_all_vehicles = Vehicle::leftJoin('tbl_vehicles_allotment', 'tbl_vehicles.vehicle_id', '=', 'tbl_vehicles_allotment.allotment_vehicle_id')
            ->where('allotment_employee_id','=',$employee_id)
            ->get()->toArray();

            $send_data = compact('title','login_user','employee_data','gadget_allotments','gadget_allotments_active','get_all_vehicles');

            return view('employee.employee-allotment', $send_data);
        }catch(\Exception $e){

            $this->create_error_log('View Employee Allotment', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View Employee Allotment');
  
        }

    }

    protected function my_personal_company_asset(){

        try{
            $title = SELF::PERSONAL_ASSETS;

            $login_user = $this->get_login_user_detail();

            $employee_data = Employees::where('employee_id', session('user_session'))->get()->toArray();

            $where_clause = 'gadget_status = 1 and tbl_gadget_allotment.allotment_id in (select max(allotment_id) from tbl_gadget_allotment group by gadget_id  )';
            $gadget_allotments_active = GadgetAllotment::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_gadget_allotment.allotment_employee_id')
            ->join('tbl_employees_gadgets', 'tbl_employees_gadgets.gadget_id', '=', 'tbl_gadget_allotment.gadget_id')
            ->whereRaw($where_clause)
            ->where('employee_id', session('user_session'))
            ->orderBy('allotment_id','DESC')
            ->pluck('allotment_id')
            ->toArray();

            $gadget_allotments = GadgetAllotment::join('tbl_employees', 'tbl_employees.employee_id', '=', 'tbl_gadget_allotment.allotment_employee_id')
            ->join('tbl_employees_gadgets', 'tbl_employees_gadgets.gadget_id', '=', 'tbl_gadget_allotment.gadget_id')
            ->where('employee_id', session('user_session'))
            ->orderBy('allotment_id','DESC')
            ->get()
            ->toArray();

            $get_all_vehicles = Vehicle::leftJoin('tbl_vehicles_allotment', 'tbl_vehicles.vehicle_id', '=', 'tbl_vehicles_allotment.allotment_vehicle_id')
            ->where('allotment_employee_id','=',session('user_session'))
            ->get()->toArray();

            $send_data = compact('title','login_user','employee_data','gadget_allotments','gadget_allotments_active','get_all_vehicles');

            return view('employee.employee-personal-assets', $send_data);

        }catch(\Exception $e){
            $this->create_error_log('View Employee Asset Allotment', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{
            ActivityTracker::track_activity('View Employee Asset Allotment');
  
        }

    }


    protected function get_on_board_employees(Request $request){

        try{
            $all_candidates = Interview::join('tbl_employees as e1','e1.employee_id','=','tbl_interviews.interview_interviewer1')
            ->leftjoin('tbl_employees as e2','e2.employee_id','=','tbl_interviews.interview_interviewer2')
            ->join('tbl_employees as e3','e3.employee_id','=','tbl_interviews.interview_created_by')
            ->join('tbl_interview_candidate','tbl_interview_candidate.candidate_id','=','tbl_interviews.interview_candidate_id')
            ->join('tbl_employee_onboarding','tbl_employee_onboarding.onboarding_candidate_id','=','tbl_interview_candidate.candidate_id')
            ->join('tbl_departments','tbl_employee_onboarding.department_name','=','tbl_departments.department_id')
            ->groupBy('onboarding_candidate_id')
            ->where('department_id','like','%' .$request->input('employee_department'). '%')
            ->Where('candidate_name','like','%' .$request->input('employee_name'). '%')->where('onboard_status',0)
            ->get(['e1.employee_name as interviewer_name1','e2.employee_name as interviewer_name2','e3.employee_name as interviewer_created_by','candidate_name','tbl_departments.department_name','interview_scheduled_time','interview_jobtitle','interview_current_salary','interview_expected_salary','tbl_employee_onboarding.onboarding_id','interview_date','interview_time'])->toArray();

            if(!empty($all_candidates)){
                return response()->json([
                    'status' => 'TRUE',
                    'data'  => $all_candidates,
                ]);
            }else{
                return response()->json([
                    'status' => 'TRUE',
                    'msg'   => 'Employee registered successfully'
                ]);
            }
        }catch(\Exception $e){
             $this->create_error_log('On board employees modal at new employee creation', $e);
             if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }finally{
            ActivityTracker::track_activity('On board employees modal at new employee creation');
        }
    }



    protected function update_onboarding_employee_status(Request $request){

        try{

            $employee_details = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->orderBy('employee_id','DESC')->offset(0)->limit(1)->get(['employee_id','employee_name','employee_gender','employee_password','employee_code','employee_personal_email','department_name','designation_name'])->toArray();

            if(!empty($request->input('onboarding_id'))){

                $update_onboard_employee =  OnBoardingEmployee::where('onboarding_id',$request->input('onboarding_id'))->update(['onboard_status' => 1]);
                if($update_onboard_employee){

                    return response()->json([
                        'status' => 'TRUE',
                        'employee_data' => $employee_details,
                        'emp_password' => Crypt::decryptString($employee_details[0]['employee_password']),
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Something went wrong. Please try again.",
                    ]);
                }

            }else{
                return response()->json([
                    'status' => 'TRUE',
                    'employee_data' => $employee_details,
                    'emp_password' => Crypt::decryptString($employee_details[0]['employee_password']),
                ]);
            }

        }catch(\Exception $e){

            $this->create_error_log('Update Status Add New OnBoard Employees', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }

        }finally{
            ActivityTracker::track_activity('Update Status Add New OnBoard Employees');
        }

    }

    protected function download_employee_document(Request $request){
        try{
            if(!empty($request->path)){
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->path));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    if($request->action == 'view'){
                        return response()->file(storage_path('app/portal_documents/'.$request->path));
                    }else{
                        return response()->download(storage_path('app/portal_documents/'.$request->path));
                    }
                }else{
                    return view('errors.404');
                }
            }else{
                return redirect(url());
            }
        }catch(\Exception $e){
            $this->create_error_log($request->title, $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }finally{
            ActivityTracker::track_activity($request->title);
        }
    }

    protected function download_personal_document(Request $request){
        try{
            if(!empty($request->path)){
                $check_if_file_exists = file_exists(storage_path('app/portal_documents/'.$request->path));
                if($check_if_file_exists == TRUE || $check_if_file_exists == 1){
                    return response()->download(storage_path('app/portal_documents/'.$request->path));
                }else{
                    return view('errors.404');
                }
            }else{
                return redirect(url());
            }
        }catch(\Exception $e){
            $this->create_error_log($request->title, $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }finally{
            ActivityTracker::track_activity($request->title);
        }
    }


    protected function get_reporting_employees(Request $request){

        $all_employee = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_senior_manager','=',$request->employee_id)->orWhere('employee_junior_manager','=',$request->employee_id)->get(['employee_id','employee_code','employee_name','department_name','designation_name'])->toArray();

        $get_employee_can_ra = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')->where('employee_isactive',1)->where('employee_is_manager',1)->where('employee_id','!=',$request->employee_id)->get(['employee_id','employee_code','employee_name','department_name','designation_name'])->toArray();

        if(!empty($all_employee)){
            return response()->json([
                'status' => 'TRUE',
                'old_reporting_manager' => $request->employee_id,
                'reporting_employee'  => $all_employee,
                'employee_is_manager'   =>  $get_employee_can_ra,
            ]);
        }else{
            return response()->json([
                'status' => 'FALSE',
            ]);
        }
    }

    protected function update_employee_can_ra(Request $request){
        try{

            foreach($request->employees as $key=>$employee){


                $get_employee_ca_ra = Employees::where('employee_senior_manager','=',$request->old_reporting_emp_id)->orWhere('employee_junior_manager','=',$request->old_reporting_emp_id)->get(['employee_senior_manager','employee_junior_manager'])->toArray();

                foreach($get_employee_ca_ra as $get_employee){

                    if($employee  ==  $request->employee_reporting_manager[$key]){
                        Employees::where('employee_id',$employee)->where('employee_senior_manager',$request->old_reporting_emp_id)->update(['employee_senior_manager'=> 1]);

                    }else{

                        if($get_employee['employee_senior_manager'] == $request->employee_reporting_manager[$key] || $get_employee['employee_junior_manager'] == $request->employee_reporting_manager[$key]){

                            Employees::where('employee_id',$employee)->where('employee_senior_manager',$request->old_reporting_emp_id)
                            ->update(['employee_senior_manager'=> $request->employee_reporting_manager[$key]]);

                            Employees::where('employee_id',$employee)->where('employee_junior_manager',$request->employee_reporting_manager[$key])->update(['employee_junior_manager'=>'']);


                            Employees::where('employee_id',$employee)->where('employee_junior_manager',$request->old_reporting_emp_id)
                            ->update(['employee_junior_manager'=> '']);



                        }

                        if($get_employee['employee_senior_manager'] == $request->old_reporting_emp_id || $get_employee['employee_junior_manager'] == $request->old_reporting_emp_id){
                            Employees::where('employee_id',$employee)->where('employee_senior_manager',$request->old_reporting_emp_id)->update(['employee_senior_manager'=> $request->employee_reporting_manager[$key]]);

                            Employees::where('employee_id',$employee)->where('employee_junior_manager',$request->old_reporting_emp_id)->update(['employee_junior_manager'=>'']);
                        }

                    }

                }

            }

                $update_employee = Employees::find($request->input('old_reporting_emp_id'));
                if(!empty($request->input('emp_deactivate_date')) && !empty($request->input('emp_deactivate_type'))){
                    $update_employee->employee_isactive = 0;
                    $update_employee->employee_deactivate_date = $request->input('emp_deactivate_date');
                    $update_employee->employee_deactivation_type = $request->input('emp_deactivate_type');

                }
                if($request->input('emp_can_ra') != null){
                    $update_employee->employee_is_manager = $request->input('emp_can_ra');
                }

                $process = $update_employee->save();

                if($process){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Employee details updated successfully'
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Employee details not updated successfully'
                    ]);
                }

        }
        catch(\Exception $e){
            $this->create_error_log('Employee Reporting Manager Updated', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }
        finally{
            ActivityTracker::track_activity('Employee Reporting Manager Updated');
        }
    }

    protected function employee_devices(Request $request){

        try{

            $title = Self::Devices;

            $login_user = $this->get_login_user_detail();

            $where_clause = '1=1';

            if(!empty($request->department_id) && $request->department_id != 'All'){
                $where_clause .= ' AND employee_department = '.$request->department_id;
            }

            if(!empty($request->business_unit) && $request->business_unit != 'All'){
                $where_clause .= " AND business_unit = '".$request->business_unit."'";
            }

            if(!empty($request->input('employee_group')) && $request->input('employee_group') != 'All'){

                $where_clause .= " and employee_group= '".$request->input('employee_group')."'";

            }

            $employee_groups = EmployeeGroup::get(['employee_group_id','group_name'])->toArray();
           
            $all_devices = EmployeeDevice::join('tbl_employees', 'tbl_employees_devices.device_employee_id', '=', 'tbl_employees.employee_id')
            ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->leftJoin('tbl_employee_groups','tbl_employees.employee_group','=','tbl_employee_groups.employee_group_id')
            ->where('device_status','!=','2')
            ->whereRaw($where_clause)
            ->get(['device_id as Device ID','device_name as Device Name','department_name as Department Name','designation_name as Designation Name','device_token as Device Token','device_status as Device Status','device_created_at as Device Created At','device_updated_at as Deviced Updated At','employee_code as Employee Code','employee_name as Employee Name', 'group_name as Employee Group'])->toArray();

            $departments = Department::get(['department_id','department_name'])->toArray();
        
            $send_data = compact('title','login_user','all_devices','departments','employee_groups');

            if($request->download == 'TRUE'){

                $all_devices = EmployeeDevice::join('tbl_employees', 'tbl_employees_devices.device_employee_id', '=', 'tbl_employees.employee_id')
                ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
                ->where('device_status','!=','2')
                ->get(['employee_code as Employee Code','employee_name as Employee Name','department_name as Department Name','designation_name as Designation Name','business_unit as Business Unit','device_name as Device Name / Model','device_token as Device Unique ID','device_created_at as App Download Time'])->toArray();

                $header_style = (new Style())->setFontBold();

                return (new FastExcel($all_devices))->headerStyle($header_style)->download('Employee-devices.xlsx');
            }else{
                return view('employee.employee-devices', $send_data);
            }

        }catch(\Exception $e){

            $this->create_error_log('Employee devices', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Employee devices');

        }

    }

    protected function approve_request(Request $request){

		try{

            $login_user = $this->get_login_user_detail();

            $get_employee_device = EmployeeDevice::find($request->input('device_id'));

            if(!empty($get_employee_device)){

               $employe_device = EmployeeDevice::where('device_employee_id',$get_employee_device->device_employee_id)->where('device_id', '!=' ,$request->input('device_id'))->get()->toArray();
                foreach($employe_device as $employee){
                    $employee = EmployeeDevice::where('device_id',$employee['device_id'])->update(['device_status'=>2]);

                }

                $get_employee_device->device_status = 1;

                $process =  $get_employee_device->save();

                if($process == 1){

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Employee device request has been approved',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Something went wrong. Please try again.',
                    ]);
                }

            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Request not found on this id.',
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Access Approve Employee Device Request', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }

        }finally{
            ActivityTracker::track_activity('Access Approve Employee Device Request');
        }
    }

    protected function disapprove_request(Request $request){

        try{
            $login_user = $this->get_login_user_detail();
            $get_employee_device = EmployeeDevice::find($request->input('device_id'));
            if(!empty($get_employee_device)){

                $get_employee_device->device_status = 2;
                $process =  $get_employee_device->save();
                if($process == 1){

                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => 'Employee device request has been disapproved',
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => 'Something went wrong. Please try again.',
                    ]);
                }

            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => 'Request not found on this id.',
                ]);
            }
        }catch(\Exception $e){
            $this->create_error_log('Access Dis-Approve Employee Device Request', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }

        }finally{
            ActivityTracker::track_activity('Access Dis-Approve Employee Device Request');

        }

    }

    // protected function download_employee_detail(Request $request){
    //     try{

    //         $security_variable = FALSE;

    //         $validator = Validator::make($request->all(), [
    //             'download_type' => ['required'],
    //             'confirmation_code' => ['required']
    //         ]);

    //         $login_user = $this->get_login_user_detail();

    //         $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];

    //         if($confirmation_code === $request->input('confirmation_code') && $validator->fails() == FALSE){
    //             $security_variable = TRUE;
    //         }
    //         else{
    //             return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/employees';</script>";
    //         }

    //         if($request->emp_status == "2"){
    //             $status = 0;
    //         }else{
    //             $status = 1;
    //         }

    //         $employees_details = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
    //         ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
    //         ->join('tbl_employement_status','tbl_employement_status.employement_status_id','=','tbl_employees.employee_status')
    //         ->leftjoin('tbl_resignation_letters', function($join){
    //             $join->on('tbl_resignation_letters.resignation_employee','=','tbl_employees.employee_id');
    //             $join->where('resignation_status', '=', '2');
    //         })
    //         ->where('employee_isactive','=',$status)
    //         ->orderBy('employee_id', 'asc')
    //         ->get(['employee_id as Employee ID','employee_name as Employee Name','employee_code as Employee Code','department_name as Department','designation_name as Designation','employee_jobtype as Employee Job Type','employement_status_name as Employee Status','employee_doj as Employee DOJ','employee_cnic as Salary Increment Date','employee_cnic as Employee Cnic','employee_cnic as Increment','employee_cnic as Gross Salary' ,'resignation_from_date as Resignation Date'])->toArray();

    //         $i = 0;
    //         foreach($employees_details as $employees_detail){

    //             $employees_details[$i]['Salary Increment Date'] = '';
    //             $employees_details[$i]['Increment'] = '';
    //             $employees_details[$i]['Gross Salary'] = '';

    //             $employee_attendance = Attendance::where('att_employee_id',$employees_detail['Employee ID'] )
    //             ->where('att_check_overall_status', '!=', 'Absent')
    //             ->orderByRaw('STR_TO_DATE(CONCAT(att_for_year,"-",att_for_month,"-",att_for_date),"%Y-%m-%d") desc')
    //             ->get(['att_for_date', 'att_for_month', 'att_for_year'])
    //             ->toArray();
    //             if(!empty($employee_attendance)){
    //                 $employees_details[$i]['Last Working Day'] = $employee_attendance[0]['att_for_year'].'-'.$employee_attendance[0]['att_for_month'].'-'.$employee_attendance[0]['att_for_date'];
    //             }else{
    //                 $employees_details[$i]['Last Working Day'] = '';
    //             }

    //             $employee_Salary = EmployeeSalary::where('salary_employee_id', $employees_detail['Employee ID'])
    //             ->whereRaw('date_format(STR_TO_DATE(salary_effected_date,"%Y-%m-%d"),"%Y-%m-%d") <= "'.date('Y-m-d').'"')
    //             ->orderByRaw('STR_TO_DATE(salary_effected_date,"%Y-%m-%d") desc')
    //             ->get(['salary_effected_date', 'salary_amount'])
    //             ->toArray();

    //             if(!empty($employee_Salary)){
    //                 $employees_details[$i]['Gross Salary'] = $employee_Salary[0]['salary_amount'];
    //                 $employees_details[$i]['Salary Increment Date'] = $employee_Salary[0]['salary_effected_date'];
    //             }else{
    //                 $employees_details[$i]['Gross Salary'] = '';
    //                 $employees_details[$i]['Salary Increment Date'] = '';
    //             }

    //             $employee_Salary = EmployeeSalary::where('salary_employee_id', $employees_detail['Employee ID'])

    //             ->whereRaw('date_format(salary_effected_date,"%m") = "'.date('m').'"')
    //             ->whereRaw('date_format(salary_effected_date,"%Y") = "'.date('Y').'"')
    //             ->whereRaw('date_format(STR_TO_DATE(salary_effected_date,"%Y-%m-%d"),"%Y-%m-%d") <= "'.date('Y-m-d').'"')
    //             ->orderByRaw('STR_TO_DATE(salary_effected_date,"%Y-%m-%d") desc')
    //             ->get(['salary_effected_date', 'salary_amount'])
    //             ->toArray();

    //             if(!empty($employee_Salary)){

    //                 $employee_Salary_increment = EmployeeSalary::where('salary_employee_id', $employees_detail['Employee ID'])
    //                 ->whereRaw('date_format(STR_TO_DATE(salary_effected_date,"%Y-%m-%d"),"%Y-%m-%d") < "'.date('Y-m-d',strtotime($employee_Salary[0]['salary_effected_date'])).'"')
    //                 ->orderByRaw('STR_TO_DATE(salary_effected_date,"%Y-%m-%d") desc')
    //                 ->get(['salary_effected_date', 'salary_amount'])
    //                 ->toArray();

    //                 if(!empty($employee_Salary_increment)){
    //                     $employees_details[$i]['Increment'] = $employee_Salary[0]['salary_amount'] - $employee_Salary_increment[0]['salary_amount'];
    //                 }else{
    //                     $employees_details[$i]['Increment'] = '';
    //                 }


    //             }else{
    //                 $employees_details[$i]['Increment'] = '';
    //             }

    //             unset($employees_details[$i]['Employee ID']);

    //             $i++;
    //         }

    //         $pdf_file_access = 'True'; 
    //         $send_data = compact('employees_details','pdf_file_access');

    //         if($security_variable == TRUE){
    //             if($request->download_type == "pdf"){
    //                 $pdf = Pdf::loadView('employee.all-employee-pdf',$send_data);
    //                 return $pdf->setPaper('a3', 'landscape')->stream('All Employee(s) Information'.'.pdf');
    //             }else{

    //                 $employees_details = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
    //                 ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
    //                 ->join('tbl_employement_status','tbl_employement_status.employement_status_id','=','tbl_employees.employee_status')
    //                 ->where('employee_isactive','=',$status)
    //                 ->orderBy('employee_id', 'asc')
    //                 ->get(['employee_code as Employee ID','employee_name as Employee Name','department_name as Department','designation_name as Designation','employee_jobtitle as Employee Job Title', 'business_unit as Business Unit'])->toArray();

    //                 return (new FastExcel($employees_details))->download('Employee-Information.xlsx');
    //             }
    //         }

    //     }catch(\Exception $e){

    //         $this->create_error_log('Employee information download', $e);
    //         return view('errors.500');

    //     }finally{

    //         ActivityTracker::track_activity('Employee information download');

    //     }
    // }

    // protected function upload_employee_record(Request $request){

    //     // try{

    //         $validator = Validator::make($request->all(), [
    //             'file' => 'required|file|mimes:csv'
    //         ]);
    //         if($validator->fails()) {
    //             foreach($validator->errors()->all() as $error){
    //                 return response()->json([
    //                     'msg' => $error,
    //                     'status' => 'FALSE',
    //                     'validation' => 'TRUE',
    //                 ]);
    //             }
    //         }else{
    //             $filename = $request->file('file')->getPathName();
    //             // dd($filename);
    //             if($request->file('file')->getSize() > 0)
    //             {
    //                 $error = 0;
    //                 $success = 0;
    //                 $file = fopen($filename, "r");
    //                 $file2 = fopen($filename, "r");
    //                 $error_logs = array();
    //                 $count_row = 0;
    //                 // dd('here');

                    

    //                 while (($getData = fgetcsv($file)) !== FALSE)
    //                 {

    //                     $count_row;

    //                     if($count_row !=0){

    //                         $employee_name = $getData[0];
    //                         // $employee_contact_no = $getData[1];
    //                         // $employee_personal_email =  $getData[2];
    //                         // $employee_nic =  $getData[3];
    //                         $employee_contact_no = '090078601' . $count_row;
    //                         $employee_personal_email =  'test@ismailindustries.com' . $count_row;
    //                         $employee_nic =  '42111-6352412-9' . $count_row;
    //                         // $employee_gender =  $getData[4];
    //                         $employee_gender =  'Male';
    //                         $employee_department =  $getData[1];
    //                         $employee_designation =  $getData[2];
    //                         $employee_job_title =  $getData[3];
    //                         // $employee_is_manager =  $getData[8];
    //                         $employee_is_manager =  1;
    //                         // $joining_date =  $getData[9];
    //                         $joining_date =  $getData[4];
    //                         // $employee_status =  $getData[10];
    //                         $employee_status =  'Permanent';
    //                         // $employee_salary =  $getData[11];
    //                         $employee_salary =  '25000';
    //                         // $employee_earning_type =  $getData[12];
    //                         $employee_earning_type =  'Monthly';
    //                         // $employee_job_type =  $getData[13];
    //                         $employee_job_type =  'Office Based';
    //                         // $employee_password =  $getData[14];
    //                         $employee_password =  '123';
    //                         // $shift_start_time =  $getData[15];
    //                         $shift_start_time =  '09:00';
    //                         // $shift_end_time =  $getData[16];
    //                         $shift_end_time =  '18:00';
    //                         // $employee_Off_day =  $getData[17];
    //                         $employee_Off_day =  'Saturday,Sunday';
    //                         // $employee_location =  $getData[18];
    //                         $employee_location =  'Head Office - I';
    //                         // $employee_permissions =  $getData[19];
    //                         $employee_permissions =  'No';

    //                         $count_record = "TRUE";

    //                         if(empty($employee_name)){
    //                             $error_msg = "Row# $count_row : Employee name required ";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }

    //                         if(empty($employee_contact_no)){
    //                             $error_msg = "Row# $count_row :  contact number required ";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $employee_contact = Employees::where('employee_contactno', $employee_contact_no)->get('employee_contactno')->toArray();

    //                             if(strlen((int)$employee_contact_no) != strlen($employee_contact_no) && !empty($employee_contact_no)){

    //                                 $error_msg = "Row# $count_row : contact number must be a number";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }

    //                             if(!empty($employee_contact)){
    //                                 $error_msg = "Row# $count_row : contact number already exist";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_personal_email)){
    //                             $error_msg = "Row# $count_row : email required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $employees_email = Employees::where('employee_personal_email', $employee_personal_email)->get('employee_personal_email')->toArray();

    //                             if(!empty( $employees_email)){
    //                                 $error_msg = "Row# $count_row : email already exist";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_nic)){
    //                             $error_msg = "Row# $count_row : Nic number required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $employees_nic = Employees::where('employee_cnic', $employee_nic)->get('employee_cnic')->toArray();

    //                             if(!empty($employees_nic)){
    //                                 $error_msg = "Row# $count_row : Nic number already exist";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_gender)){
    //                             $error_msg = "Row# $count_row : Gender required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $employment_gender_field  = ['Male','Female'];

    //                             if(!in_array($employee_gender,  $employment_gender_field)){
    //                                 $error_msg = "Row# $count_row : Unknown employee gender field";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_department)){
    //                             $error_msg = "Row# $count_row : department name required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $departments = Department::where('department_name', $employee_department)->get(['department_id','department_name'])->toArray();

    //                             if(empty($departments)){
    //                                 $error_msg = "Row# $count_row : Unknown department name";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_designation)){
    //                             $error_msg = "Row# $count_row : designation required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $designations = Department::join('tbl_designations', 'tbl_departments.department_id', '=', 'tbl_designations.department_id')
    //                             ->where('designation_name', $employee_designation)
    //                             ->where('department_name', $employee_department)
    //                             ->get(['designation_id','designation_name','tbl_designations.department_id','department_name'])->toArray();

    //                             if(empty($designations)){
    //                                 $error_msg = "Row# $count_row : Unknown designation name";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_job_title)){
    //                             $error_msg = "Row# $count_row : Employee job title required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }

    //                         if(empty($employee_is_manager)){
    //                             $error_msg = "Row# $count_row : Employee can ra required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }

    //                         if($employee_is_manager  == 'Yes'){
    //                             $employee_senior_manager = 1 ;
    //                         }elseif($employee_is_manager  == 'No') {
    //                             $employee_senior_manager= 0;
    //                         }else{
    //                             $error_msg = "Row# $count_row : Unknown employee can ra type";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }

    //                         if(empty($joining_date)){
    //                             $error_msg = "Row# $count_row : Date of joining required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $dateTime = DateTime::createFromFormat('Y-m-d', $joining_date);

    //                             if ($dateTime == TRUE) {
    //                                 $joining_date = $dateTime->format('Y-m-d');
    //                             }else {
    //                                 $error_msg = "Row# $count_row : Date of joning must be a date";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_status)){
    //                             $error_msg = "Row# $count_row : Employement status required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $employement_status = EmploymentStatus::where('employement_status_name', $employee_status)->get(['employement_status_id','employement_status_name'])->toArray();

    //                             if(empty($employement_status)){
    //                                 $error_msg = "Row# $count_row : Unknown Employement status";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_salary)){
    //                             $error_msg = "Row# $count_row : Employee salary required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             if(strlen((int)$employee_salary) != strlen($employee_salary) && !empty($employee_salary)){

    //                                 $error_msg = "Row# $count_row : employee salary must be number";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);

    //                             }
    //                         }

    //                         if(empty($employee_earning_type)){
    //                             $error_msg = "Row# $count_row : Employee earning type required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             if($employee_earning_type == 'Monthly'){
    //                                 $employee_earning = 1;
    //                             }elseif($employee_earning_type == 'Daily wages') {
    //                                 $employee_earning = 2;
    //                             }else{
    //                                 $error_msg = "Row# $count_row : Unknown earning type";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_job_type)){
    //                             $error_msg = "Row# $count_row : Employee job type required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             if($employee_job_type == 'Office Based'){
    //                                 $employee_job = 'Office';
    //                             }elseif($employee_job_type == 'Home Based') {
    //                                 $employee_job= 'Home';
    //                             }else{
    //                                 $error_msg = "Row# $count_row : Unknown employee job type";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_password)){
    //                             $error_msg = "Row# $count_row : Employee password required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }

    //                         if(empty($shift_start_time)){
    //                             $error_msg = "Row# $count_row : shift start time required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $dateTime = DateTime::createFromFormat('H:i', $shift_start_time);

    //                             if ($dateTime == TRUE) {
    //                                 $shift_start_time = $dateTime->format('H:i');
    //                             }else {
    //                                 $error_msg = "Row# $count_row : shift start time must be a time";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($shift_end_time)){
    //                             $error_msg = "Row# $count_row : shift end time required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $dateTime = DateTime::createFromFormat('H:i', $shift_end_time);

    //                             if ($dateTime == TRUE) {
    //                                 $shift_end_time = $dateTime->format('H:i');
    //                             }else {
    //                                 $error_msg = "Row# $count_row : shift end time must be a time";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_Off_day)){
    //                             $error_msg = "Row# $count_row : Employee off day required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $employment_off_day_field  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

    //                             $employee_Off_day = explode(',',$employee_Off_day);

    //                             if (array_intersect($employee_Off_day , $employment_off_day_field) != $employee_Off_day ) {

    //                                 $error_msg = "Row# $count_row : Unknown employement off day field";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_location)){
    //                             $error_msg = "Row# $count_row : location required";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $locations = OfficeLocation::where('location_title', $employee_location)->get('location_id')->toArray();

    //                             if(empty($locations)){
    //                                 $error_msg = "Row# $count_row : Unknown location";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if(empty($employee_permissions)){
    //                             $error_msg = "Row# $count_row : Employee permission required ";
    //                             $count_record = "FALSE";
    //                             array_push($error_logs, $error_msg);
    //                         }else{

    //                             $employment_permission  = ['Yes','No'];

    //                             if(!in_array($employee_permissions,  $employment_permission)){
    //                                 $error_msg = "Row# $count_row : Unknown employee permission field";
    //                                 $count_record = "FALSE";
    //                                 array_push($error_logs, $error_msg);
    //                             }
    //                         }

    //                         if($count_record == "TRUE"){
    //                             $success++;
    //                         }else{
    //                             $error++;
    //                         }


    //                     }
    //                     $count_row ++;

    //                 }
    //                 if(empty($error_msg) || $request->input('Force_Approved') == 'TRUE'){
    //                     $count_row = 0;
    //                     while (($getData = fgetcsv($file2)) !== FALSE)
    //                     {

    //                         if($count_row !=0){

    //                             $employee_name = $getData[0];
    //                             $employee_contact_no = $getData[1];
    //                             $employee_personal_email =  $getData[2];
    //                             $employee_nic =  $getData[3];
    //                             $employee_gender =  $getData[4];
    //                             $employee_department =  $getData[5];
    //                             $employee_designation =  $getData[6];
    //                             $employee_job_title =  $getData[7];
    //                             $employee_is_manager =  $getData[8];
    //                             $joining_date =  $getData[9];
    //                             $employee_status =  $getData[10];
    //                             $employee_salary =  $getData[11];
    //                             $employee_earning_type =  $getData[12];
    //                             $employee_job_type =  $getData[13];
    //                             $employee_password =  $getData[14];
    //                             $shift_start_time =  $getData[15];
    //                             $shift_end_time =  $getData[16];
    //                             $employee_Off_day =  $getData[17];
    //                             $employee_location =  $getData[18];
    //                             $employee_permissions =  $getData[19];

    //                             $employee_record_insert = "TRUE";

    //                             if(empty($employee_name)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_contact_no)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $employee_contact = Employees::where('employee_contactno', $employee_contact_no)->get('employee_contactno')->toArray();

    //                             if(strlen((int)$employee_contact_no) != strlen($employee_contact_no) && !empty($employee_contact_no)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(!empty($employee_contact)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_personal_email)){
    //                                 $employee_record_insert = "FALSE";
    //                             }
    //                             $employees_email = Employees::where('employee_personal_email', $employee_personal_email)->get('employee_personal_email')->toArray();

    //                             if(!empty( $employees_email)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_nic)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $employees_nic = Employees::where('employee_cnic', $employee_nic)->get('employee_cnic')->toArray();

    //                             if(!empty($employees_nic)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_gender)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $employment_gender_field  = ['Male','Female'];

    //                             if(!in_array($employee_gender,  $employment_gender_field)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_department)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $departments = Department::where('department_name', $employee_department)->get(['department_id','department_name'])->toArray();

    //                             if(empty($departments)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_designation)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $designations = Department::join('tbl_designations', 'tbl_departments.department_id', '=', 'tbl_designations.department_id')
    //                             ->where('designation_name', $employee_designation)
    //                             ->where('department_name', $employee_department)
    //                             ->get(['designation_id','designation_name','tbl_designations.department_id','department_name'])->toArray();

    //                             if(empty($designations)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_job_title)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_is_manager)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if($employee_is_manager  == 'Yes'){
    //                                 $employee_senior_manager = '1' ;
    //                             }elseif($employee_is_manager  == 'No') {
    //                                 $employee_senior_manager= '0';
    //                             }else{
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($joining_date)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $dateTime = DateTime::createFromFormat('Y-m-d', $joining_date);

    //                             if ($dateTime == TRUE) {
    //                                 $joining_date = $dateTime->format('Y-m-d');
    //                             }else {
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_status)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $employement_status = EmploymentStatus::where('employement_status_name', $employee_status)->get(['employement_status_id','employement_status_name'])->toArray();

    //                             if(empty($employement_status)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_salary)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(strlen((int)$employee_salary) != strlen($employee_salary) && !empty($employee_salary)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_earning_type)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if($employee_earning_type == 'Monthly'){
    //                                 $employee_earning = 1;
    //                             }elseif($employee_earning_type == 'Daily wages') {
    //                                 $employee_earning = 2;
    //                             }else{
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_job_type)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if($employee_job_type == 'Office Based'){
    //                                 $employee_job = 'Office';
    //                             }elseif($employee_job_type == 'Home Based') {
    //                                 $employee_job= 'Home';
    //                             }else{
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_password)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($shift_start_time)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $dateTime = DateTime::createFromFormat('H:i', $shift_start_time);

    //                             if ($dateTime == TRUE) {
    //                                 $shift_start_time = $dateTime->format('H:i');
    //                             }else {
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($shift_end_time)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $dateTime = DateTime::createFromFormat('H:i', $shift_end_time);

    //                             if ($dateTime == TRUE) {
    //                                 $shift_end_time = $dateTime->format('H:i');
    //                             }else {
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_Off_day)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $employment_off_day_field  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    //                             $employee_Off_day = explode(',',$employee_Off_day);

    //                             if (array_intersect($employee_Off_day , $employment_off_day_field) != $employee_Off_day ) {

    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_location)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $locations = OfficeLocation::where('location_title', $employee_location)->get('location_id')->toArray();

    //                             if(empty($locations)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if(empty($employee_permissions)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             $employment_permission  = ['Yes','No'];

    //                             if(!in_array($employee_permissions,  $employment_permission)){
    //                                 $employee_record_insert = "FALSE";
    //                             }

    //                             if($employee_record_insert == "TRUE"){

    //                                 $all_employees = Employees::orderBy('employee_id','DESC')->limit(1)->get(['employee_code'])->toArray();
    //                                 if(empty($all_employees)){
    //                                     $employeeCode = '001';
    //                                 }else{
    //                                     $employeeCode = '00'. ++ $all_employees[0]['employee_code'];
    //                                 }
    //                                 $add_employee = new Employees();
    //                                 $add_employee->employee_name = $employee_name;
    //                                 $add_employee->employee_code = $employeeCode;
    //                                 $add_employee->employee_doj =  date('Y-m-d',strtotime($joining_date));
    //                                 $add_employee->employee_cnic = $employee_nic;
    //                                 $add_employee->employee_personal_email = $employee_personal_email;
    //                                 $add_employee->employee_contactno = $employee_contact_no;
    //                                 $add_employee->employee_gender = $employee_gender;
    //                                 $add_employee->employee_status =  $employement_status[0]['employement_status_id'];
    //                                 $add_employee->employee_designation = $designations[0]['designation_id'];
    //                                 $add_employee->employee_department = $departments[0]['department_id'];
    //                                 $add_employee->employee_jobtitle = $employee_job_title;
    //                                 $add_employee->employee_password = Crypt::encryptString($employee_password);
    //                                 $add_employee->employee_senior_manager =  '1';
    //                                 $add_employee->employee_is_manager = $employee_senior_manager;
    //                                 $add_employee->employee_offdays = (!empty($employee_Off_day)) ? implode(',',$employee_Off_day) : '';
    //                                 $add_employee->employee_jobtype = $employee_job;
    //                                 $add_employee->employee_location = $locations[0]['location_id'];
    //                                 $add_employee->employee_isactive =  1;
    //                                 $add_employee->employee_earning_type = $employee_earning;

    //                                 $employee = $add_employee->save();
    //                                 $employee_registration_id = $add_employee->employee_id;

    //                                 if($employee == 1){

    //                                     $emp_salary = new EmployeeSalary();
    //                                     $emp_salary->salary_employee_id = $employee_registration_id;
    //                                     $emp_salary->salary_amount =  $employee_salary;
    //                                     $emp_salary->salary_effected_date = $joining_date;
    //                                     $emp_salary->salary_created_date = date('d-m-Y');
    //                                     $emp_salary->salary_earning_type = $employee_earning_type;
    //                                     $emp_salary->save();

    //                                     $add_leaves =  new Leave();
    //                                     $add_leaves->leaves_employee_id = $employee_registration_id;
    //                                     $add_leaves->leave_type_id      = 1;
    //                                     $add_leaves->leave_allotement   = 0;
    //                                     $add_leaves->leave_ent_created_at =  date('Y-m-d H:i:s');
    //                                     $add_leaves->save();

    //                                     $add_leaves =  new Leave();
    //                                     $add_leaves->leaves_employee_id = $employee_registration_id;
    //                                     $add_leaves->leave_type_id      = 2;
    //                                     $add_leaves->leave_allotement   = 0;
    //                                     $add_leaves->leave_ent_created_at =  date('Y-m-d H:i:s');
    //                                     $add_leaves->save();

    //                                     $add_leaves =  new Leave();
    //                                     $add_leaves->leaves_employee_id = $employee_registration_id;
    //                                     $add_leaves->leave_type_id      = 3;
    //                                     $add_leaves->leave_allotement   = 0;
    //                                     $add_leaves->leave_ent_created_at =  date('Y-m-d H:i:s');
    //                                     $add_leaves->save();

    //                                     $add_shift = new ShiftManagement();
    //                                     $add_shift->employee_id = $employee_registration_id;
    //                                     $add_shift->shift_start_from_date = $joining_date;
    //                                     $add_shift->shift_start_time = $shift_start_time.':00';
    //                                     $add_shift->shift_end_time = $shift_end_time.':00';
    //                                     $add_shift->shift_created_by = session('user_session');
    //                                     $process = $add_shift->save();

    //                                     if($process == 1){

    //                                         $this->send_notification('Welcome To The Team', 'Dear employee, We warmly welcome you in our team. Kindly fill employee detail form by clicking on this notification','personal-information', $employee_registration_id,NULL,NULL,'no');

    //                                         $submitInformation = new EmployeeInformation();
    //                                         $submitInformation->employeeid = $employee_registration_id;
    //                                         $submitInformation->save();

    //                                         $get_holidays =  Holiday::where('holiday_date','>=', $joining_date)->where(function ($query) use ($departments) {
    //                                             $query->where('department_name', 'LIKE', '%'.$departments[0]['department_id']. '%')
    //                                             ->orWhere('department_name', 'LIKE', '%' .'*'. '%');
    //                                         })
    //                                         ->get()->toArray();

    //                                         if(!empty($get_holidays)){
    //                                             foreach($get_holidays as $get_holiday){
    //                                                 $day = date("d", strtotime($get_holiday['holiday_date']));
    //                                                 $month = date("m", strtotime($get_holiday['holiday_date']));
    //                                                 $year = date("Y", strtotime($get_holiday['holiday_date']));

    //                                                 $mark_attendance = new Attendance();
    //                                                 $mark_attendance->att_employee_id =  $employee_registration_id;
    //                                                 $mark_attendance->att_check_in_time = '00:00:00';
    //                                                 $mark_attendance->att_check_out_time = '00:00:00';
    //                                                 $mark_attendance->att_check_in_status = '0';
    //                                                 $mark_attendance->att_check_out_status = 0;
    //                                                 $mark_attendance->att_shift_start_time = $shift_start_time->input('shift_start_timings').':00';
    //                                                 $mark_attendance->att_shift_end_time = $shift_end_time->input('shift_end_timings').':00';
    //                                                 $mark_attendance->att_check_overall_status = 'Holiday : '.$get_holiday['holiday_name'].'';
    //                                                 $mark_attendance->att_for_date = $day;
    //                                                 $mark_attendance->att_for_month = $month;
    //                                                 $mark_attendance->att_for_year = $year;
    //                                                 $mark_attendance->save();


    //                                             }
    //                                         }

    //                                         $mypermissions = [

    //                                             '/dashboard',
    //                                             '/my-attendance',
    //                                             '/my-attendance-request',
    //                                             '/my-leaves',
    //                                             '/my-interviews',
    //                                             '/payslip',
    //                                             '/my-meetings',
    //                                             '/my-wfh-requests',
    //                                             '/employee-resignation',
    //                                             '/my-salary-request',
    //                                             '/my-loan',
    //                                             '/my-tickets',
    //                                             '/my-expense',
    //                                             '/my-shift-time',
    //                                             '/permissions',
    //                                         ];

    //                                         $permissionRecords = [
    //                                             '/dashboard',
    //                                             '/my-attendance',
    //                                             '/my-attendance-request',
    //                                             '/my-leaves',
    //                                             '/my-interviews',
    //                                             '/payslip',
    //                                             '/my-meetings',
    //                                             '/my-wfh-requests',
    //                                             '/employee-resignation',
    //                                             '/my-salary-request',
    //                                             '/my-loan',
    //                                             '/my-tickets',
    //                                             '/employees',
    //                                             '/employees-managers',
    //                                             '/employee-birthdays',
    //                                             '/employee-work-anniversay',
    //                                             '/gadgets-inventory',
    //                                             '/gadgets-allotment',
    //                                             '/meetings',
    //                                             '/attendance',
    //                                             '/attendance-requests',
    //                                             '/daily-attendance-report',
    //                                             '/wfh-requests',
    //                                             '/employees-leaves',
    //                                             '/manage-leaves',
    //                                             '/interviews',
    //                                             '/employee-onboarding',
    //                                             '/payroll',
    //                                             '/petty-cash',
    //                                             '/daily-expense',
    //                                             '/loans',
    //                                             '/holidays',
    //                                             '/departments',
    //                                             '/designations',
    //                                             '/vehicles',
    //                                             '/login-history',
    //                                             '/system-settings',
    //                                             '/system-error-logs',
    //                                             '/resignations',
    //                                             '/alerts',
    //                                             '/requisition',
    //                                             '/my-requisition',
    //                                             '/letters',
    //                                             '/all-salary-request',
    //                                             '/all-loan-request',
    //                                             '/all-tickets',
    //                                             '/permissions',
    //                                             '/tax-slab',
    //                                             '/tax-collection',
    //                                             '/rooms',
    //                                             '/locations',
    //                                             '/candiddate-interviews',
    //                                             '/employee-devices',
    //                                             '/linked-in-post',
    //                                             '/shift-management',
    //                                             '/employee-expense',
    //                                             '/overtime-management',
    //                                             '/organogram',
    //                                             '/my-expense',
    //                                             '/my-shift-time',
    //                                             '/employee-letter',
    //                                             '/letter-template',
    //                                             '/machine-attendance-logs',
    //                                             '/leave-management',
    //                                             '/polling',
    //                                             '/shift-time-request'


    //                                         ];

    //                                         foreach($permissionRecords as $permissionRecord ){
    //                                             $permissions = new Permission();
    //                                             $permissions->module_url = $permissionRecord;
    //                                             $permissions->employee_id = $employee_registration_id;
    //                                             if($employee_permissions == "No"){
    //                                                 $permissions->permission_status = in_array($permissionRecord, $mypermissions) ? 1 : 0;
    //                                             }else{
    //                                                 $permissions->permission_status = 1 ;
    //                                             }
    //                                             $permissions->save();

    //                                         }

    //                                     }
    //                                 }
    //                                 $success++;
    //                             }

    //                         }
    //                         $count_row ++;


    //                     }
    //                     fclose($file2);

    //                     return response()->json([
    //                         'status' => 'TRUE',
    //                         'msg' => 'Upload employees record',
    //                         'error_logs' => $error_logs
    //                     ]);
    //                 }else{
    //                     $msg = "You have $success valid records and $error invalid records.";

    //                     return response()->json([
    //                         'status' => 'FALSE',
    //                         'msg' =>  'Employee record upload failed '."<br>".$msg,
    //                         'error_logs' => $error_logs
    //                     ]);
    //                 }

    //             }


    //         }
    //     // }catch(\Exception $e){
    //     //     $this->create_error_log('Upload employee information', $e);
    //     //     return response()->json([
    //     //         'status' => 'FALSE',
    //     //         'msg' => 'Something went wrong. Please try again.',
    //     //     ]);
    //     // }finally{
    //     //     ActivityTracker::track_activity('Upload employee information');

    //     // }
    // }

    protected function upload_employee_record(Request $request){

        // try{
            set_time_limit(18000);
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:csv'
            ]);
            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE',
                        'validation' => 'TRUE',
                    ]);
                }
            }else{
                $filename = $request->file('file')->getPathName();
                // dd($filename);
                if($request->file('file')->getSize() > 0)
                {
                    $error = 0;
                    $success = 0;
                    $file = fopen($filename, "r");
                    $file2 = fopen($filename, "r");
                    $error_logs = array();
                    $count_row = 0;
                    // dd('here');

                    

                    // while (($getData = fgetcsv($file)) !== FALSE)
                    // {

                    //     $count_row;

                    //     if($count_row !=0){
                    //         // dd($getData[0][1]);
                    //         $employee_name = $getData[0];
                    //         // $employee_contact_no = $getData[1];
                    //         // $employee_personal_email =  $getData[2];
                    //         // $employee_nic =  $getData[3];
                    //         $employee_contact_no = '090078601' . $count_row;
                    //         $employee_personal_email =  'test@gmail.com' . $count_row;
                    //         $employee_nic =  '48943-0987654-0' . $count_row;
                    //         $employee_gender =  $getData[4];
                    //         $employee_department =  $getData[5];
                    //         $employee_designation =  $getData[6];
                    //         $employee_job_title =  $getData[7];
                    //         $employee_is_manager =  $getData[8];
                    //         $joining_date =  $getData[9];
                    //         $employee_status =  $getData[10];
                    //         $employee_salary =  $getData[11];
                    //         $employee_earning_type =  $getData[12];
                    //         $employee_job_type =  $getData[13];
                    //         $employee_password =  $getData[14];
                    //         $shift_start_time =  $getData[15];
                    //         $shift_end_time =  $getData[16];
                    //         $employee_Off_day =  $getData[17];
                    //         $employee_location =  $getData[18];
                    //         $employee_permissions =  $getData[19];

                    //         $count_record = "TRUE";

                    //         if(empty($employee_name)){
                    //             $error_msg = "Row# $count_row : Employee name required ";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }

                    //         if(empty($employee_contact_no)){
                    //             $error_msg = "Row# $count_row :  contact number required ";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $employee_contact = Employees::where('employee_contactno', $employee_contact_no)->get('employee_contactno')->toArray();

                    //             if(strlen((int)$employee_contact_no) != strlen($employee_contact_no) && !empty($employee_contact_no)){

                    //                 $error_msg = "Row# $count_row : contact number must be a number";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }

                    //             if(!empty($employee_contact)){
                    //                 $error_msg = "Row# $count_row : contact number already exist";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_personal_email)){
                    //             $error_msg = "Row# $count_row : email required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $employees_email = Employees::where('employee_personal_email', $employee_personal_email)->get('employee_personal_email')->toArray();

                    //             if(!empty( $employees_email)){
                    //                 $error_msg = "Row# $count_row : email already exist";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_nic)){
                    //             $error_msg = "Row# $count_row : Nic number required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $employees_nic = Employees::where('employee_cnic', $employee_nic)->get('employee_cnic')->toArray();

                    //             if(!empty($employees_nic)){
                    //                 $error_msg = "Row# $count_row : Nic number already exist";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_gender)){
                    //             $error_msg = "Row# $count_row : Gender required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $employment_gender_field  = ['Male','Female'];

                    //             if(!in_array($employee_gender,  $employment_gender_field)){
                    //                 $error_msg = "Row# $count_row : Unknown employee gender field";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_department)){
                    //             $error_msg = "Row# $count_row : department name required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $departments = Department::where('department_name', $employee_department)->get(['department_id','department_name'])->toArray();

                    //             if(empty($departments)){
                    //                 $error_msg = "Row# $count_row : Unknown department name";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_designation)){
                    //             $error_msg = "Row# $count_row : designation required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $designations = Department::join('tbl_designations', 'tbl_departments.department_id', '=', 'tbl_designations.department_id')
                    //             ->where('designation_name', $employee_designation)
                    //             ->where('department_name', $employee_department)
                    //             ->get(['designation_id','designation_name','tbl_designations.department_id','department_name'])->toArray();

                    //             if(empty($designations)){
                    //                 $error_msg = "Row# $count_row : Unknown designation name";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_job_title)){
                    //             $error_msg = "Row# $count_row : Employee job title required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }

                    //         if(empty($employee_is_manager)){
                    //             $error_msg = "Row# $count_row : Employee can ra required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }

                    //         if($employee_is_manager  == 'Yes'){
                    //             $employee_senior_manager = 1 ;
                    //         }elseif($employee_is_manager  == 'No') {
                    //             $employee_senior_manager= 0;
                    //         }else{
                    //             $error_msg = "Row# $count_row : Unknown employee can ra type";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }

                    //         if(empty($joining_date)){
                    //             $error_msg = "Row# $count_row : Date of joining required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $dateTime = DateTime::createFromFormat('Y-m-d', $joining_date);

                    //             if ($dateTime == TRUE) {
                    //                 $joining_date = $dateTime->format('Y-m-d');
                    //             }else {
                    //                 $error_msg = "Row# $count_row : Date of joning must be a date";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_status)){
                    //             $error_msg = "Row# $count_row : Employement status required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $employement_status = EmploymentStatus::where('employement_status_name', $employee_status)->get(['employement_status_id','employement_status_name'])->toArray();

                    //             if(empty($employement_status)){
                    //                 $error_msg = "Row# $count_row : Unknown Employement status";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_salary)){
                    //             $error_msg = "Row# $count_row : Employee salary required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             if(strlen((int)$employee_salary) != strlen($employee_salary) && !empty($employee_salary)){

                    //                 $error_msg = "Row# $count_row : employee salary must be number";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);

                    //             }
                    //         }

                    //         if(empty($employee_earning_type)){
                    //             $error_msg = "Row# $count_row : Employee earning type required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             if($employee_earning_type == 'Monthly'){
                    //                 $employee_earning = 1;
                    //             }elseif($employee_earning_type == 'Daily wages') {
                    //                 $employee_earning = 2;
                    //             }else{
                    //                 $error_msg = "Row# $count_row : Unknown earning type";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_job_type)){
                    //             $error_msg = "Row# $count_row : Employee job type required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             if($employee_job_type == 'Office Based'){
                    //                 $employee_job = 'Office';
                    //             }elseif($employee_job_type == 'Home Based') {
                    //                 $employee_job= 'Home';
                    //             }else{
                    //                 $error_msg = "Row# $count_row : Unknown employee job type";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_password)){
                    //             $error_msg = "Row# $count_row : Employee password required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }

                    //         if(empty($shift_start_time)){
                    //             $error_msg = "Row# $count_row : shift start time required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $dateTime = DateTime::createFromFormat('H:i', $shift_start_time);

                    //             if ($dateTime == TRUE) {
                    //                 $shift_start_time = $dateTime->format('H:i');
                    //             }else {
                    //                 $error_msg = "Row# $count_row : shift start time must be a time";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($shift_end_time)){
                    //             $error_msg = "Row# $count_row : shift end time required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $dateTime = DateTime::createFromFormat('H:i', $shift_end_time);

                    //             if ($dateTime == TRUE) {
                    //                 $shift_end_time = $dateTime->format('H:i');
                    //             }else {
                    //                 $error_msg = "Row# $count_row : shift end time must be a time";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_Off_day)){
                    //             $error_msg = "Row# $count_row : Employee off day required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $employment_off_day_field  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

                    //             $employee_Off_day = explode(',',$employee_Off_day);

                    //             if (array_intersect($employee_Off_day , $employment_off_day_field) != $employee_Off_day ) {

                    //                 $error_msg = "Row# $count_row : Unknown employement off day field";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_location)){
                    //             $error_msg = "Row# $count_row : location required";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $locations = OfficeLocation::where('location_title', $employee_location)->get('location_id')->toArray();

                    //             if(empty($locations)){
                    //                 $error_msg = "Row# $count_row : Unknown location";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if(empty($employee_permissions)){
                    //             $error_msg = "Row# $count_row : Employee permission required ";
                    //             $count_record = "FALSE";
                    //             array_push($error_logs, $error_msg);
                    //         }else{

                    //             $employment_permission  = ['Yes','No'];

                    //             if(!in_array($employee_permissions,  $employment_permission)){
                    //                 $error_msg = "Row# $count_row : Unknown employee permission field";
                    //                 $count_record = "FALSE";
                    //                 array_push($error_logs, $error_msg);
                    //             }
                    //         }

                    //         if($count_record == "TRUE"){
                    //             $success++;
                    //         }else{
                    //             $error++;
                    //         }


                    //     }
                    //     $count_row ++;

                    // }
                    // if(empty($error_msg) || $request->input('Force_Approved') == 'TRUE'){
                        $arrays = [];
                        $count_row = 0;
                        while (($getData = fgetcsv($file2)) !== FALSE)
                        {

                            // if($count_row != 0){
                                // dd($getData[1]);
                                // dd('here');

                                $employeeCode = preg_replace('/^[\x00-\x1F\x80-\xFF]+/u', '', iconv('ISO-8859-1', 'UTF-8', $getData[0]));//$getData[0];
                                $employee_name = iconv('ISO-8859-1', 'UTF-8', $getData[1]);//$getData[1];
                                $employee_job_title =  iconv('ISO-8859-1', 'UTF-8', $getData[2]);
                                $employee_designation =  iconv('ISO-8859-1', 'UTF-8', $getData[3]);
                                $employee_department =  iconv('ISO-8859-1', 'UTF-8', $getData[4]); //$getData[5];
                                $joining_date =  iconv('ISO-8859-1', 'UTF-8', $getData[5]);
                                // $employee_gender = iconv('ISO-8859-1', 'UTF-8', $getData[6]);
                                // $employee_senior = iconv('ISO-8859-1', 'UTF-8', $getData[7]);
                                // $employee_status = iconv('ISO-8859-1', 'UTF-8', $getData[8]);
                                // $employee_salary =  iconv('ISO-8859-1', 'UTF-8', $getData[6]); //$getData[11];
                                // $employee_Off_day =  iconv('ISO-8859-1', 'UTF-8', $getData[7]); //$getData[17];
                                // $employee_dob =  iconv('ISO-8859-1', 'UTF-8', $getData[8]); //$getData[10];
                                $employee_contact_no = '21312313128' . $count_row;
                                // $employee_address =  iconv('ISO-8859-1', 'UTF-8', $getData[10]); //$getData[10];
                                // $employee_business_unit =  iconv('ISO-8859-1', 'UTF-8', $getData[11]); //$getData[7];
                                // $employee_nic = iconv('ISO-8859-1', 'UTF-8', $getData[4]);
                                $employee_personal_email =  'test@gmail.com' . $employeeCode;
                                
                             
                                // $employee_job_title =  iconv('ISO-8859-1', 'UTF-8', $getData[7]); //$getData[7];
                                // $employee_is_manager =  iconv('ISO-8859-1', 'UTF-8', $getData[8]); //$getData[8];
                        
                                // $employee_status =  iconv('ISO-8859-1', 'UTF-8', $getData[10]); //$getData[10];
                             
                                // $employee_earning_type = iconv('ISO-8859-1', 'UTF-8',  $getData[12]); // $getData[12];
                                // $employee_job_type =  iconv('ISO-8859-1', 'UTF-8', $getData[13]); //$getData[13];
                                // $employee_password =  iconv('ISO-8859-1', 'UTF-8', $getData[14]); //$getData[14];
                                // $shift_start_time =  iconv('ISO-8859-1', 'UTF-8', $getData[15]); //$getData[15];
                                // $shift_end_time = iconv('ISO-8859-1', 'UTF-8',  $getData[16]); // $getData[16];
                                // $employee_Off_day =  iconv('ISO-8859-1', 'UTF-8', $getData[17]); //$getData[17];
                                // $employee_location =  iconv('ISO-8859-1', 'UTF-8', $getData[18]); //$getData[18];
                                // $employee_permissions =  iconv('ISO-8859-1', 'UTF-8', $getData[19]); //$getData[19];

                                $employee_record_insert = "TRUE";

                                // if(empty($employee_name)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_contact_no)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // $employee_contact = Employees::where('employee_contactno', $employee_contact_no)->get('employee_contactno')->toArray();

                                // if(strlen((int)$employee_contact_no) != strlen($employee_contact_no) && !empty($employee_contact_no)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(!empty($employee_contact)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_personal_email)){
                                //     $employee_record_insert = "FALSE";
                                // }
                                // $employees_email = Employees::where('employee_personal_email', $employee_personal_email)->get('employee_personal_email')->toArray();

                                // if(!empty( $employees_email)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_nic)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // $employees_nic = Employees::where('employee_cnic', $employee_nic)->get('employee_cnic')->toArray();

                                // if(!empty($employees_nic)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_gender)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // $employment_gender_field  = ['Male','Female'];

                                // if(!in_array($employee_gender,  $employment_gender_field)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_department)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // $check_senior_manager_code = Employees::where('employee_code', '=', $employee_senior)->get('employee_id')->toArray();
                                // if(empty($check_senior_manager_code)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                $departments = Department::where('department_name', $employee_department)->get(['department_id','department_name'])->toArray();

                                if(empty($departments)){
                                    $employee_record_insert = "FALSE";
                                }

                                // if(empty($employee_designation)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                $designations = Department::join('tbl_designations', 'tbl_departments.department_id', '=', 'tbl_designations.department_id')
                                ->where('designation_name', $employee_designation)
                                ->where('department_name', $employee_department)
                                ->get(['designation_id','designation_name','tbl_designations.department_id','department_name'])->toArray();

                                if(empty($designations)){
                                    $employee_record_insert = "FALSE";
                                }

                                array_push($arrays , $employee_designation);

                                // if(empty($employee_job_title)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_is_manager)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if($employee_is_manager  == 'Yes'){
                                //     $employee_senior_manager = '1' ;
                                // }elseif($employee_is_manager  == 'No') {
                                //     $employee_senior_manager= '0';
                                // }
                                // else{
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($joining_date)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                $dateTime = DateTime::createFromFormat('Y-m-d', $joining_date);

                                if ($dateTime == TRUE) {
                                    $joining_date = $dateTime->format('Y-m-d');
                                }else {
                                    $employee_record_insert = "FALSE";
                                }

                                // if($employee_gender == 'M'){
                                //     $employee_gender_type = 'Male';
                                // }elseif($employee_gender == 'F') {
                                //     $employee_gender_type = 'Female';
                                // }else{
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_status)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // $employement_status = EmploymentStatus::where('employement_status_name', $employee_status)->get(['employement_status_id','employement_status_name'])->toArray();

                                // if(empty($employement_status)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_salary)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(strlen((int)$employee_salary) != strlen($employee_salary) && !empty($employee_salary)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_earning_type)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if($employee_earning_type == 'Monthly'){
                                //     $employee_earning = 1;
                                // }elseif($employee_earning_type == 'Daily wages') {
                                //     $employee_earning = 2;
                                // }else{
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_job_type)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if($employee_job_type == 'Office Based'){
                                //     $employee_job = 'Office';
                                // }elseif($employee_job_type == 'Home Based') {
                                //     $employee_job= 'Home';
                                // }
                                // else{
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_password)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($shift_start_time)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // $dateTime = DateTime::createFromFormat('H:i', $shift_start_time);

                                // if ($dateTime == TRUE) {
                                //     $shift_start_time = $dateTime->format('H:i');
                                // }else {
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($shift_end_time)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // $dateTime = DateTime::createFromFormat('H:i', $shift_end_time);

                                // if ($dateTime == TRUE) {
                                //     $shift_end_time = $dateTime->format('H:i');
                                // }else {
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_Off_day)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // $employment_off_day_field  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                                // $employee_Off_day = explode(',',$employee_Off_day);

                                // if (array_intersect($employee_Off_day , $employment_off_day_field) != $employee_Off_day ) {

                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_location)){
                                //     $employee_record_insert = "FALSE";
                                // }
                                // $employee_location = 'Temporary Undefined Location';
                                // $locations = OfficeLocation::where('location_title', $employee_location)->get('location_id')->toArray();

                                // if(empty($locations)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // if(empty($employee_permissions)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                // $employment_permission  = ['Yes','No'];

                                // if(!in_array($employee_permissions,  $employment_permission)){
                                //     $employee_record_insert = "FALSE";
                                // }

                                if(1 == 1){

                                    // $all_employees = Employees::orderBy('employee_id','DESC')->limit(1)->get(['employee_code'])->toArray();
                                    // if(empty($all_employees)){
                                    //     $employeeCode = '001';
                                    // }else{
                                    //     $employeeCode = '00'. ++ $all_employees[0]['employee_code'];
                                    // }

                                    $check_employee_code = Employees::where('employee_code', '=', $employeeCode)->get('employee_code')->toArray();
                                
                                    if(empty($check_employee_code)){
                            
                                        $add_employee = new Employees();
                                        $add_employee->employee_name = $employee_name;
                                        $add_employee->employee_code =  $employeeCode;
                                        // $add_employee->employee_code = $employeeCode;
                                        $add_employee->employee_doj =  date('Y-m-d',strtotime($joining_date));
                                        // $add_employee->employee_cnic = $employee_nic;
                                        $add_employee->employee_personal_email = $employee_personal_email;
                                        $add_employee->employee_work_email = $employee_personal_email;
                                        $add_employee->employee_contactno = $employee_contact_no;
                                        $add_employee->employee_gender = 'Male';
                                        $add_employee->employee_status =  3;
                                        $add_employee->employee_designation = ($designations[0]['designation_id']) ?? 1;
                                        $add_employee->employee_department = $departments[0]['department_id'];
                                        $add_employee->employee_jobtitle = $employee_job_title;
                                        $add_employee->employee_password = Crypt::encryptString(123);
                                        $add_employee->employee_geofencing_enability = false;
                                        $add_employee->employee_senior_manager = 1;
                                        $add_employee->employee_is_manager = 0;
                                        $add_employee->employee_offdays = 'Sunday';
                                        $add_employee->employee_jobtype = 'Office';
                                        // $add_employee->employee_location = $locations[0]['location_id'];
                                        $add_employee->employee_isactive =  1;
                                        $add_employee->employee_earning_type =  1;
                                        // $add_employee->business_unit = $employee_business_unit;

                                        $employee = $add_employee->save();
                                        $employee_registration_id = $add_employee->employee_id;

                                        if($employee == 1){

                                            // $new_employee_location = new EmployeeLocation();
                                            // $new_employee_location->emp_loc_employee_id = $employee_registration_id;
                                            // $new_employee_location->emp_loc_loc_id = $locations[0]['location_id'];
                                            // $new_employee_location->created_at = date('Y-m-d H:i:s');
                                            // $new_employee_location->save();

                                            $emp_salary = new EmployeeSalary();
                                            $emp_salary->salary_employee_id = $employee_registration_id;
                                            $emp_salary->salary_amount =  15000;
                                            $emp_salary->salary_effected_date = date('Y-m-d',strtotime($joining_date));
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
                                            $add_shift->shift_start_from_date = date('Y-m-d',strtotime($joining_date));
                                            $add_shift->shift_start_time = '09:00:00';
                                            $add_shift->shift_end_time = '18:00:00';
                                            $add_shift->shift_created_by = session('user_session');
                                            $process = $add_shift->save();

                                            $employment_period = new EmploymentPeriod();
                                            $employment_period->employement_status_id = 3;
                                            $employment_period->employee_id = $employee_registration_id;
                                            $employment_period->effective_date = date('Y-m-d',strtotime($joining_date));
                                            $employment_period->employement_period_created_by = 1;
                                            $employment_period->employement_period_created_at = date('Y-m-d H:i:s');
                                            $process = $employment_period->save();

                                            if($process == 1){

                                                $this->send_notification('Welcome To The Team', 'Dear employee, We warmly welcome you in our team. Kindly fill employee detail form by clicking on this notification','personal-information', $employee_registration_id,NULL,NULL,'no');

                                                $submitInformation = new EmployeeInformation();
                                                $submitInformation->employeeid = $employee_registration_id;
                                                // $submitInformation->dob = $employee_dob;
                                                // $submitInformation->permanentaddress = $employee_address;
                                                $submitInformation->save();

                                                $get_holidays =  Holiday::where('holiday_date','>=', date('Y-m-d',strtotime($joining_date)))->where(function ($query) use ($departments) {
                                                    $query->where('department_name', 'LIKE', '%'.$departments[0]['department_id']. '%')
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
                                                        $mark_attendance->att_shift_start_time = $shift_start_time->input('shift_start_timings').':00';
                                                        $mark_attendance->att_shift_end_time = $shift_end_time->input('shift_end_timings').':00';
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
                                                    '/employee-fund'
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

                                            }
                                        }   
                                    }
                                    $success++;
                                }

                            // }
                            // $count_row ++;


                        }
                        // dd($arrays);
                        fclose($file2);

                        return response()->json([
                            'status' => 'TRUE',
                            'msg' => 'Upload employees record',
                            'error_logs' => $error_logs
                        ]);
                    // }
                    // else{
                    //     $msg = "You have $success valid records and $error invalid records.";

                    //     return response()->json([
                    //         'status' => 'FALSE',
                    //         'msg' =>  'Employee record upload failed '."<br>".$msg,
                    //         'error_logs' => $error_logs
                    //     ]);
                    // }

                }


            }
        // }catch(\Exception $e){
        //     $this->create_error_log('Upload employee information', $e);
        //     return response()->json([
        //         'status' => 'FALSE',
        //         'msg' => 'Something went wrong. Please try again.',
        //     ]);
        // }finally{
        //     ActivityTracker::track_activity('Upload employee information');

        // }
    }

    protected function employees_location_detail(Request $request){

        try{
            $title = Self::EMP_LOCATION;

            $login_user = $this->get_login_user_detail();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if($request->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1' ;

            if(!empty($request->employee_location)){
                $where_clause .= " AND location_id = '".$request->employee_location."'";

            }

            $employee_location_detail = Employees::where('employee_isactive','=',$status)
            ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->join('tbl_employees_location', 'tbl_employees.employee_id', '=', 'tbl_employees_location.emp_loc_employee_id')
            ->join('tbl_office_locations', 'tbl_employees_location.emp_loc_loc_id', '=', 'tbl_office_locations.location_id')
            ->whereRaw($where_clause)
            ->get(['employee_code as Employee ID','employee_name as Employee Name', 'department_name as Department', 'designation_name as Designation', 'business_unit as Business unit', 'location_title as Location title', 'location_latitude as Location latitude', 'location_longitude as Location longitude'])->toArray();

            $send_data = compact('title','login_user','employee_location_detail','employee_locations');

            if($request->download == 'TRUE'){
                $header_style = (new Style())->setFontBold();
                return (new FastExcel($employee_location_detail))->headerStyle($header_style)->download('Employee-locations.xlsx');
            }else{
                return view('employee.employee-location', $send_data);
            }

        }
        catch(\Exception $e){

            $this->create_error_log('View All Employees Location Details', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Employees Location Details');

        }
	}

    protected function employees_bank_detail(Request $request){

        try{
            $title = Self::BANK_TITLE;

            $login_user = $this->get_login_user_detail();

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if($request->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1' ;

            if(!empty($request->emp_name)){
                $where_clause .= ' AND employee_id = "'.$request->emp_name.'"';
            }
            if(!empty($request->employee_location)){
                $where_clause .= ' AND employee_location = "'.$request->employee_location.'"';

            }

            $employee_bank = Employees::where('employee_isactive','=',$status)
            ->join('tbl_personal_informations', 'tbl_employees.employee_id', '=', 'tbl_personal_informations.employeeid')
            ->whereRaw($where_clause)
            ->get(['employee_id','employee_code','employee_name','bankname','bankcode','accountno','accounttitle','ibannumber', 'employee_location'])->toArray();

            $send_data = compact('title','login_user','employee_bank','employee_locations');
            
            if($request->input('download') == 'TRUE'){
                $pdf = Pdf::loadView('employee.download-employee-bank-pdf', $send_data);
                return $pdf->setPaper('a3', 'potrait')->stream('All Employee(s) Bank details'.'.pdf');
            }else{
                return view('employee.employee-bank-details', $send_data);
            }

        }
        catch(\Exception $e){

            $this->create_error_log('View All Employees Bank Details', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('View All Employees Bank Details');

        }
	}

    protected function download_employees_bank_detail(Request $request){

        try{

            $security_variable = FALSE;

            $validator = Validator::make($request->all(), [
                'confirmation_code' => ['required']
            ]);

            $login_user = $this->get_login_user_detail();

            $confirmation_code = 'TODP-'.$login_user[0]['employee_code'];

            if($confirmation_code === $request->input('confirmation_code') && $validator->fails() == FALSE){
                $security_variable = TRUE;
            }
            else{
                return "<script>alert('You have enterd incorrect information. Please try again.'); window.location.href = '/employee-bank';</script>";
            }

            $employee_locations = OfficeLocation::get(['location_id','location_title'])->toArray();

            if($request->emp_status == "2"){
                $status = 0;
            }else{
                $status = 1;
            }

            $where_clause = '1=1' ;

            if(!empty($request->emp_name)){
                $where_clause .= ' AND employee_id = "'.$request->emp_name.'"';
            }
            if(!empty($request->employee_location)){
                $where_clause .= ' AND employee_location = "'.$request->employee_location.'"';

            }

            $employee_bank = Employees::where('employee_isactive','=',$status)
            ->join('tbl_personal_informations', 'tbl_employees.employee_id', '=', 'tbl_personal_informations.employeeid')
            ->whereRaw($where_clause)
            ->get(['employee_id','employee_code','employee_name','bankname','bankcode','accountno','accounttitle','ibannumber', 'employee_location'])->toArray();

            $send_data = compact('login_user','employee_bank','employee_locations','status');

            if($security_variable == TRUE){

                $pdf = Pdf::loadView('employee.download-employee-bank-pdf',$send_data);
                return $pdf->setPaper('a3', 'landscape')->stream('All Employee(s) Bank details'.'.pdf');
            }
        }
        catch(\Exception $e){

            $this->create_error_log('download All Employees Bank Details', $e);
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Download All Employees Bank Details pdf');

        }
	}


    protected function employment_status(Request $request){

        try{
            $title = Self::EMP_STATUS;

            $login_user = $this->get_login_user_detail();

            $get_employment_status = EmploymentStatus::get()->toArray();

            $employement_status_id = array_column($get_employment_status, 'employement_status_id');

            $employment_status_id = Employees::whereIn('employee_status',$employement_status_id)->pluck('employee_status')->toArray();

            $employment_status_id = array_unique($employment_status_id);

            $send_data = compact('title','login_user','get_employment_status','employment_status_id');

            return view('employee.all-employment-status', $send_data);

        }
        catch(\Exception $e){

            $this->create_error_log('Employment Status Module Access', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Employment Status Module Access');

        }
	}

    protected function add_employment_status(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'employment_status' => ['required','unique:tbl_employement_status,employement_status_name','regex:/^[\pL\s\&\,(\)]+$/u'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{
                $add_employee_status =  new EmploymentStatus();
                $add_employee_status->employement_status_name = $request->input('employment_status');
                $add_employee_status->employement_status_created_by = session('user_session');
                $add_employee_status->employement_status_created_at = date('Y-m-d H:i:s');
                $process = $add_employee_status->save();
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Employment status added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Employment status not added successfully. Please try again after some time.",
                    ]);
                }
            }

        }
        catch(\Exception $e){

            $this->create_error_log('Add Employment Status', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }

        }finally{

            ActivityTracker::track_activity('Add Employment Status');


        }
	}

    protected function update_employment_status(Request $request){

        try{
            $validator = Validator::make($request->all(),[
                'employment_status' => ['required','unique:tbl_employement_status,employement_status_name,' .$request->input('edit_employment_status_id'). ' ,employement_status_id', 'regex:/^[\pL\s\&\,(\)]+$/u'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{
                $update_employee_status =   EmploymentStatus::find($request->input('edit_employment_status_id'));

                $employement_status_name = EmploymentStatus::whereIn('employement_status_name',array('Intern','Probation','Permanent','PartTime'))->pluck('employement_status_name')->toArray();
                if(!empty($update_employee_status)){

                    $employment_status_count = Employees::where('employee_status',$update_employee_status->employement_status_id)->count();

                    if(!in_array($update_employee_status->employement_status_name, $employement_status_name) &&  $employment_status_count <= 0 ){

                        $update_employee_status->employement_status_name = $request->input('employment_status');
                        $update_employee_status->employement_status_created_by = session('user_session');
                        $update_employee_status->employement_status_created_at = date('Y-m-d H:i:s');
                        $process = $update_employee_status->save();
                        if($process == 1){
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Employment status updated successfully.",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Employment status not updated successfully. Please try again after some time.",
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This employement status can't be update now, employee(s) are registered on it.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "This employment status id not found.",
                    ]);
                }
            }

        }
        catch(\Exception $e){
            $this->create_error_log('Updated Employment Status', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }

        }finally{
            ActivityTracker::track_activity('Updated Employment Status');

        }
	}

    protected function delete_employee_status($employment_id){
        try{

            if(!empty($employment_id) && is_numeric($employment_id)){
                $check_if_employment_exists = EmploymentStatus::where('employement_status_id', $employment_id)->get()->toArray();

                if(!empty($check_if_employment_exists)){
                   
                    $employment_status_count = Employees::where('employee_status',$check_if_employment_exists[0]['employement_status_id'])->count();

                    if(!in_array($check_if_employment_exists[0]['employement_status_name'],array('Intern','Probation','Permanent','PartTime')) && $employment_status_count <= 0){
                       $emp_delete =  EmploymentStatus::where('employement_status_id',$employment_id)->delete();

                        if($emp_delete == 1){
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Employment status deleted successfully ",
                            ]);
                       }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "Employment status not deleted successfully try again.",
                        ]);
                       }

                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This employement status can't be deleted now, employee(s) are registered on it.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Employment status id not found.",

                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Employment status id is required",
                ]);
            }
        }catch(\Exception $e){

            $this->create_error_log("Employment Status Deleted", $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }finally{

		    ActivityTracker::track_activity('Delete Employment Status');

        }
    }

    protected function employee_groups(Request $request){

        try{
            $title = Self::EMP_GROUP;

            $login_user = $this->get_login_user_detail();

            $get_employee_groups = EmployeeGroup::
            select('employee_group_id','group_name','created_at', DB::raw('COUNT(employee_id) as total_employees'))
            ->leftJoin('tbl_employees','tbl_employee_groups.employee_group_id','=','tbl_employees.employee_group')
            ->groupBy('employee_group_id','group_name','created_at')
            ->get()
            ->toArray();

            $employee_group_id = array_column($get_employee_groups, 'employee_group_id');

            $employee_group_id = Employees::whereIn('employee_group', $employee_group_id)->pluck('employee_group')->toArray();

            $send_data = compact('title','login_user','get_employee_groups','employee_group_id');

            return view('employee.employee-groups', $send_data);

        }
        catch(\Exception $e){

            $this->create_error_log('Employee Group Module Access', $e);
            $send_data = compact('title');
            return view('errors.500');

        }finally{

            ActivityTracker::track_activity('Employee Group Module Access');

        }
	}

    protected function add_employee_group(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'employee_group_name' => ['required','unique:tbl_employee_groups,group_name','regex:/^[\pL\s\&\,(\)]+$/u'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{
                $add_employee_status = new EmployeeGroup();
                $add_employee_status->group_name = $request->input('employee_group_name');
                $add_employee_status->created_by = session('user_session');
                $add_employee_status->created_at = date('Y-m-d H:i:s');
                $process = $add_employee_status->save();
                if($process == 1){
                    return response()->json([
                        'status' => 'TRUE',
                        'msg' => "Employee group added successfully.",
                    ]);
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Employee group not added successfully. Please try again after some time.",
                    ]);
                }
            }

        }
        catch(\Exception $e){

            $this->create_error_log('Add Employee Group', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }

        }finally{

            ActivityTracker::track_activity('Add Employee Group');


        }
	}

    protected function update_employee_group(Request $request){

        try{
            $validator = Validator::make($request->all(),[
                'employee_group_name' => ['required','unique:tbl_employee_groups,group_name,' .$request->input('edit_employee_group_id'). ' ,employee_group_id', 'regex:/^[\pL\s\&\,(\)]+$/u'],
            ]);

            if($validator->fails()) {
                foreach($validator->errors()->all() as $error){
                    return response()->json([
                        'msg' => $error,
                        'status' => 'FALSE'
                    ]);
                }
            }else{
                $update_employee_group = EmployeeGroup::find($request->input('edit_employee_group_id'));

                // $employement_status_name = EmployeeGroup::whereIn('employement_status_name',array('Intern','Probation','Permanent','PartTime'))->pluck('employement_status_name')->toArray();
                if(!empty($update_employee_group)){

                    $employee_group_count = Employees::where('employee_group', $request->input('edit_employee_group_id'))->count();

                    if($employee_group_count <= 0 ){

                        $update_employee_group->group_name = $request->input('employee_group_name');
                        $update_employee_group->created_by = session('user_session');
                        $update_employee_group->created_at = date('Y-m-d H:i:s');
                        $process = $update_employee_group->save();
                        if($process == 1){
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Employee group updated successfully.",
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Employee group not updated successfully. Please try again after some time.",
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This employee group can't be update now, employee(s) are registered on it.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "This Employee group id not found.",
                    ]);
                }
            }

        }
        catch(\Exception $e){
            $this->create_error_log('Updated Employee Group', $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }

        }finally{
            ActivityTracker::track_activity('Updated Employee Group');

        }
	}

    protected function delete_employee_group($employee_group_id){
        try{

            if(!empty($employee_group_id) && is_numeric($employee_group_id)){
                $check_if_employee_group_exists = EmployeeGroup::where('employee_group_id', $employee_group_id)->get()->toArray();

                if(!empty($check_if_employee_group_exists)){

                    $employees_grouped_count = Employees::where('employee_group', $employee_group_id)->count();

                    if($employees_grouped_count <= 0){
                        $emp_delete = EmployeeGroup::where('employee_group_id', $employee_group_id)->delete();
                        if($emp_delete == 1){
                            return response()->json([
                                'status' => 'TRUE',
                                'msg' => "Employee group deleted successfully.",
                            ]);
                       }else{
                            return response()->json([
                                'status' => 'FALSE',
                                'msg' => "Employee group not deleted successfully try again.",
                            ]);
                        }
                    }else{
                        return response()->json([
                            'status' => 'FALSE',
                            'msg' => "This employee group can't be deleted now, employee(s) are registered on it.",
                        ]);
                    }
                }else{
                    return response()->json([
                        'status' => 'FALSE',
                        'msg' => "Employee group id not found.",

                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Employee group id is required",
                ]);
            }
        }catch(\Exception $e){

            $this->create_error_log("Employment Status Deleted", $e);
            if(!env('APP_DEBUG')){
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",

                ]);
            }else{
                return response()->json([
                    'status' => 'FALSE',
                    'msg' => "Something went wrong. Please try again.",
                    'error' =>'Error on line '.$e->getLine().' '.$e->getMessage(),
                ]);

            }
        }finally{

		    ActivityTracker::track_activity('Delete Employment Status');

        }
    }

    public function checkValidationExists(Request $request)
    {
        $exists = '';
        if(!empty($request->input('email'))){
            $exists = Employees::where('employee_personal_email',$request->input('email'))->orWhere('employee_work_email',$request->input('email'))->exists();

        }elseif(!empty($request->input('email_update'))){
           
            $exists = Employees::where('employee_personal_email',$request->input('email_update'))->orWhere('employee_work_email',$request->input('email_update'))->where('employee_id','!=',$request->input('employee_id'))->exists();

        }
        if(!empty($request->input('contact'))){
            $exists = Employees::where('employee_contactno',$request->input('contact'))->exists();
        }elseif(!empty($request->input('contact_update'))){
            $exists = Employees::where('employee_contactno',$request->input('contact_update'))->where('employee_id','!=',$request->input('employee_id'))->exists();
        }
        if(!empty($request->input('cnic'))){
            $exists = Employees::where('employee_cnic',$request->input('cnic'))->exists();
        }elseif(!empty($request->input('cnic_update'))){
            $exists = Employees::where('employee_cnic',$request->input('cnic_update'))->where('employee_id','!=',$request->input('employee_id'))->exists();
        }
      
        return response()->json(
            [
                'exists' => $exists
            ]
        );
    }

}
