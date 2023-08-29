<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Employees, Payslip, Leave, EmployeeInformation, Attendance, Interview, Alert, AlertStatus, LateComers, ExpenseSheet, Meeting, MeetingUsers, Permission,LeaveManagement};
use App\Traits\{LogGenerateTrait, LoginEmployee, ApiResponseTrait};
use App\Helpers\{ActivityTracker, CustomHelper};
use Illuminate\Support\Facades\File;

class HomeControllerApi extends Controller
{   
    use LogGenerateTrait, LoginEmployee, ApiResponseTrait;

    const MAIN_TITLE = "Dashboard";
    
    protected function index(Request $request){
        try{
            
            $title = Self::MAIN_TITLE;

            
            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));
            $permission = CustomHelper::access_permission_from_api($login_user[0]['employee_id']);
          
            if(!empty($login_user[0]['profile_avatar'])){
                if(file_exists('portal_assets/profile_pictures/'.$login_user[0]['profile_avatar'])){
                    $employee_profile = asset('portal_assets/profile_pictures')."/".$login_user[0]['profile_avatar'];
                }else{
                    $employee_profile = null;
                }
            }else{
                $employee_profile = null;
            }

            $login_user_birthday = FALSE;
            // if employee has birthday then condition will be TRUE default is FALSE
            if(!empty($login_user) && date('d-m', strtotime($login_user[0]['dob'])) == date('d-m')){
                $login_user_birthday = TRUE;
            }
            
            $welcome_greeting = CustomHelper::employee_welcome_status_api($login_user[0]['employee_id']);
            $employee_statistics = ['status' => FALSE, 'data' => []];
            
            if(!empty($permission) && $permission[12]['module_accessibility'] == 1 && $permission[12]['permission_status'] == 1){

                $employee_statistics['status'] = TRUE;

                $active_employees = Employees::where('employee_isactive', 1)->count();

                $employee_statistics['data']['active_employees'] = $active_employees;

                $active_male_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Male')->count();

                $employee_statistics['data']['active_male_employees'] = $active_male_employees;

                $active_female_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Female')->count();

                $employee_statistics['data']['active_female_employees'] = $active_female_employees;

                $deactive_employees = Employees::where('employee_isactive', 0)->count();

                $employee_statistics['data']['deactive_employees'] = $deactive_employees;

                $deactive_male_employees = Employees::where('employee_isactive', 0)->where('employee_gender','Male')->count();

                $employee_statistics['data']['deactive_male_employees'] = $deactive_male_employees;

                $deactive_female_employees = Employees::where('employee_isactive', 0)->where('employee_gender','Female')->count();

                $employee_statistics['data']['deactive_female_employees'] = $deactive_female_employees;

                $total_employees =  $active_employees + $deactive_employees;

                $employee_statistics['data']['total_employees'] = $total_employees;

                $permanent_employees = Employees::where('employee_isactive', 1)->where('employee_status',3)->count();

                $employee_statistics['data']['permanent_employees'] = $permanent_employees;

                $permanent_male_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Male')->where('employee_status',3)->count();

                $employee_statistics['data']['permanent_male_employees'] = $permanent_male_employees;

                $permanent_female_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Female')->where('employee_status',3)->count();
                
                $employee_statistics['data']['permanent_female_employees'] = $permanent_female_employees;
                
                $probation_employees = Employees::where('employee_isactive', 1)->where('employee_status',2)->count();

                $employee_statistics['data']['probation_employees'] = $probation_employees;

                $probation_male_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Male')->where('employee_status',2)->count();

                $employee_statistics['data']['probation_male_employees'] = $probation_male_employees;

                $probation_female_employees = Employees::where('employee_isactive', 1)->where('employee_gender','Female')->where('employee_status',2)->count();

                $employee_statistics['data']['probation_female_employees'] = $probation_female_employees;

            }

            // $payslips = Payslip::where('employee_slip_month',date('F'))->where('employee_slip_year',date('Y'))->count();

            $leaves_entitiled = LeaveManagement::join('tbl_leave_entitlement','tbl_leave_management.leave_manage_id','=','tbl_leave_entitlement.leave_type_id')->WhereIN('leave_type_id',array(1,2,3))->where('leaves_employee_id',$login_user[0]['employee_id'])->get(['leave_name','leave_allotement'])->toArray();

            $annual_used = Attendance::where('att_employee_id', $login_user[0]['employee_id'])->where('att_check_overall_status','Annual Leave')->count();

            $casual_used = Attendance::where('att_employee_id', $login_user[0]['employee_id'])->where('att_check_overall_status','Casual Leave')->count();

            $sick_used = Attendance::where('att_employee_id', $login_user[0]['employee_id'])->where('att_check_overall_status','Sick Leave')->count();

            $todays_interviews = Interview::where('interview_date',date('Y-m-d'))->count();


            if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2){
                $where_clause = 'interview_date LIKE  "%'.date("Y-m-d").'%"';
            }else{
                $where_clause = 'interview_date LIKE  "%'.date("Y-m-d").'%"  and ( interview_interviewer1 = '.$login_user[0]['employee_id'].' or interview_interviewer2 = '.$login_user[0]['employee_id'].' )';
            }
            
            $interviews = Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','=','tbl_interview_candidate.candidate_id')->whereRaw($where_clause)->get(['interview_id','candidate_name','interview_date','interview_time','interview_interviewer1','interview_interviewer2','interview_scheduled_time'])->toArray();

            $alert_status = AlertStatus::where('alert_status_employee_id', $login_user[0]['employee_id'])->pluck('alert_status_alert_id')->toArray();
           
            $alerts = Alert::whereRaw('STR_TO_DATE(scheduled_time, "%Y-%m-%d %H:%i:%s") <  "'.date('Y-m-d H:i:s').'" and STR_TO_DATE(valid_dates, "%Y-%m-%d %H:%i:%s") >  "'.date('Y-m-d H:i:s').'" ')
            ->where(function($query) use($login_user) {
                $query->where('department', 'LIKE', '%' .$login_user[0]['employee_department']. '%')->orWhere('department', 'LIKE', '%*%');
                })->whereNotIn('alert_id', $alert_status)->get()->toArray();
            

            
            $employee_birthdays = ['status' => FALSE, 'data' => []];

            if(!empty($permission) &&  $permission[14]['module_accessibility'] == 1 && $permission[14]['permission_status'] == 1){ 

                $employee_birthdays['status'] = TRUE;

                $birthdays = Employees::join('tbl_personal_informations', 'tbl_employees.employee_id', '=','tbl_personal_informations.employeeid')
                ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
                ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
                ->where('employee_isactive',1)
                ->whereNotNull('dob')
                ->whereMonth('dob','=',date('m'))
                ->orderByRaw('DAY(dob)')
                ->get(['employee_id','department_name as employee_department','designation_name as employee_designation','dob'])
                ->toArray();

                $employee_birthdays['data'] = $birthdays;
                
            }

            // concat("'.asset('portal_assets/profile_pictures').'/" ,profile_avatar) as
            $senior_manager = Employees::selectRaw('employee_name, profile_avatar')->join('tbl_personal_informations', 'tbl_employees.employee_id', '=', 'tbl_personal_informations.employeeid')->where('employee_id','=',$login_user[0]['employee_ra'])->get()->toArray();
          
            if(!empty($senior_manager[0]['profile_avatar'])){
                if(file_exists('portal_assets/profile_pictures/'.$senior_manager[0]['profile_avatar'])){
                    $senior_manager[0]['profile_avatar'] = asset('portal_assets/profile_pictures')."/".$senior_manager[0]['profile_avatar'];
                }else{
                    $senior_manager[0]['profile_avatar'] = null;
                }
            }else{
                $senior_manager[0]['profile_avatar'] = null;
            }
           
            $junior_manager = Employees::selectRaw('employee_name,profile_avatar')->join('tbl_personal_informations', 'tbl_employees.employee_id', '=', 'tbl_personal_informations.employeeid')->where('employee_id','=',$login_user[0]['employee_ra2'])->get()->toArray();
            
            if(!empty($junior_manager[0]['profile_avatar'])){
                if(file_exists('portal_assets/profile_pictures/'.$junior_manager[0]['profile_avatar'])){
                    $junior_manager[0]['profile_avatar']  = asset('portal_assets/profile_pictures')."/".$junior_manager[0]['profile_avatar'];
                }else{
                    $junior_manager[0]['profile_avatar'] = null;
                }
            }else{
                $junior_manager[0]['profile_avatar'] = null;
            }

            $bad_attendance_employees = ['status' => FALSE, 'data' => [] ];

            if(!empty($permission) && $permission[12]['module_accessibility'] == 1 && $permission[12]['permission_status'] == 1){

                $bad_attendance_employees['status'] = TRUE;

                $bad_attendance_employees_get = LateComers::join('tbl_employees','tbl_continous_latecomers.employee_id','=','tbl_employees.employee_id')->where('employee_isactive', 1)->where('create_date' , date('Y-m-d'))->get(['tbl_employees.employee_id','employee_name','employee_code','attendance_status','create_date'])->toArray();
                
                $bad_attendance_employees['data'] = $bad_attendance_employees_get;

            }
            
            $my_team_mates = ['status' => FALSE, 'data' => []];

            if($login_user[0]['employee_can_ra'] == 1){

                $total_team_member = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'],1);

                $my_team_mates['status'] = TRUE;

                $my_team_mates_get = Employees::selectRaw('employee_name,department_name as employee_department,designation_name as employee_designation, concat("'.asset('portal_assets/profile_pictures').'/" ,profile_avatar) as profile_avatar')
                ->join('tbl_personal_informations', 'tbl_employees.employee_id', '=', 'tbl_personal_informations.employeeid')
                ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
                ->where(function($query) use($login_user,$total_team_member) {
                    $query->where('employee_id','!=',$login_user[0]['employee_id'])->whereIn('employee_id',$total_team_member);
                })->get()->toArray();

                $my_team_mates['data'] = $my_team_mates_get;
            }

            $current_date = date('Y-m-d');
            $next_date = date('Y-m-d', strtotime("+1 day"));
            

            $meeting_count_user = Meeting::join('tbl_meeting_users','tbl_meeting_users.users_meeting_id','=','tbl_meetings.meeting_id')
            ->where(function ($query) use ($current_date,$next_date) {
                $query->where('meeting_time','like','%'.$current_date.'%')
                ->orWhere('meeting_time','like','%'.$next_date.'%');
            })->where('user_id',$login_user[0]['employee_id'])->where('meeting_status',1)->count();
           

            $meeting_count_host = Meeting::where(function ($query) use ($current_date,$next_date) {
                $query->where('meeting_time','like','%'.$current_date.'%')
                ->orWhere('meeting_time','like','%'.$next_date.'%');
            })->where('host_id',$login_user[0]['employee_id'])->where('meeting_status',1)->count();
           
            $total_meeting_count = $meeting_count_user + $meeting_count_host;
            
            $attendance_summary = CustomHelper::attendance_summary_employee_from_api($login_user[0]['employee_id']);

            $data = [
                'title' => $title,
                'login_user_birthday' => $login_user_birthday,
                'login_user' => $login_user,
                'leaves_entitiled' => $leaves_entitiled,
                'annual_used' => $annual_used,
                'casual_used' => $casual_used,
                'sick_used' => $sick_used,
                'todays_interviews' => $todays_interviews,
                'senior_manager' => $senior_manager,
                'junior_manager' => $junior_manager,
                'interviews' => $interviews,
                'alerts' => $alerts,
                'total_meeting_count' => $total_meeting_count,
                'attendance_summary' => $attendance_summary,
                'my_team_mates' => $my_team_mates,
                'employee_statistics' => $employee_statistics,
                'employee_birthdays' => $employee_birthdays,
                'bad_attendance_employees' => $bad_attendance_employees,
                'welcome_greeting' => $welcome_greeting,
                'employee_profile' => $employee_profile,
            ];

            return $this->api_response_json(TRUE,"Dashboard records.",$data,200);

        }catch(\Exception $e){

            $this->create_error_log_from_api('Employees Dashboard API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);

        }finally{

            ActivityTracker::track_activity_from_api('Access Dashboard Module API',  $request->header('Bearer-Token'));
            
        }
    }


    protected function sidebar(Request $request){

        try{

            $login_user = $this->get_token_user_detail($request->header('Bearer-Token'));

            $permission = Permission::where('employee_id', $login_user[0]['employee_id'])->where('permission_status',1)->join('tbl_system_modules','tbl_system_modules.module_url','tbl_employee_permission.module_url')->where('module_accessibility',1)->pluck('module_id');
          
            $companySetting = CustomHelper::hcm_default_settings();

            $data = ['company_setting' => $companySetting, 'permission' => $permission];

            return $this->api_response_json(TRUE,"Sidebar record.",$data,200);


        }catch(\Exception $e){

            $this->create_error_log_from_api('Employees Dashboard API', $e, $request->header('Bearer-Token'));

            return $this->api_response_json(FALSE,"Something went wrong. Please try again.",$e->getMessage(),500);


        }
    }

}
